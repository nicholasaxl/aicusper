<?php

namespace App\Http\Controllers;

use App\Models\UnifiedInventory;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * The product page, the menu sync and the AI labelling all work on unified_inventory_table:
 * a flat per-outlet copy of the POS menu. POS tables are only touched by "Sync menu".
 */
class ProductController extends Controller
{
    private const MAX_MENU_ITEMS = 150; // the AI service accepts at most this many items per request

    /** Menu of the outlet the logged-in user is attached to. */
    public function index(Request $request)
    {
        $outlet = $this->currentOutlet($request);

        if (!$outlet) {
            return view('product.index', [
                'outlet'     => null,
                'inventory'  => new LengthAwarePaginator([], 0, 10),
                'lastSynced' => null,
            ]);
        }

        $query = UnifiedInventory::where('outlet_id', $outlet->id);

        // 🔍 Search by name
        if ($request->filled('name')) {
            $query->where('name', 'ILIKE', '%' . $request->name . '%');
        }

        // 📝 Search by description
        if ($request->filled('description')) {
            $query->where('description', 'ILIKE', '%' . $request->description . '%');
        }

        // 🟢🔴 Active status
        if ($request->filled('active')) {
            $query->where('is_active', $request->active === 'active');
        }

        // 💰 Price
        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }

        $inventory = $query->orderBy('name')->paginate(10)->withQueryString(); // keep filters on pagination
        $lastSynced = UnifiedInventory::where('outlet_id', $outlet->id)->max('synced_at');

        return view('product.index', compact('outlet', 'inventory', 'lastSynced'));
    }

    /**
     * Pull the outlet's menu from the POS tables into unified_inventory_table.
     * New items are added, changed ones updated, and items the outlet no longer sells are marked inactive.
     * AI labels are kept unless the item's name/category/description changed.
     */
    public function syncMenu(Request $request)
    {
        $outlet = $this->currentOutlet($request);
        if (!$outlet) {
            return back()->with('error', 'Your account is not attached to an outlet.');
        }

        $posItems = $this->posMenuQuery($outlet->id)->get();
        $existing = UnifiedInventory::where('outlet_id', $outlet->id)->get()->keyBy('item_id');
        $now = DB::raw('CURRENT_TIMESTAMP'); // let Postgres stamp it: avoids a UTC-vs-server-zone skew

        $rows = [];
        $added = $updated = 0;
        $labelsStale = [];

        foreach ($posItems as $item) {
            $old = $existing->get($item->item_id);
            $row = [
                'merchant_id' => (int) $outlet->merchant_id,
                'outlet_id'   => (int) $outlet->id,
                'item_id'     => (int) $item->item_id,
                'name'        => $item->name,
                'category'    => $item->category,
                'description' => $item->description,
                'price'       => $item->price,
                'image_path'  => $item->image_path,
                'is_active'   => (bool) $item->is_active,
                'synced_at'   => $now,
            ];
            $rows[] = $row;

            if (!$old) {
                $added++;
                continue;
            }

            $textChanged = $old->name !== $row['name']
                || $old->category !== $row['category']
                || $old->description !== $row['description'];

            if ($textChanged) {
                $labelsStale[] = $row['item_id'];
            }
            if ($textChanged
                || (float) $old->price !== (float) $row['price']
                || $old->image_path !== $row['image_path']
                || $old->is_active !== $row['is_active']) {
                $updated++;
            }
        }

        $deactivated = 0;
        DB::transaction(function () use ($rows, $posItems, $outlet, $labelsStale, $now, &$deactivated) {
            foreach (array_chunk($rows, 500) as $chunk) {
                // "labels" is not in the update list, so existing labels survive a sync
                UnifiedInventory::upsert(
                    $chunk,
                    ['outlet_id', 'item_id'],
                    ['merchant_id', 'name', 'category', 'description', 'price', 'image_path', 'is_active', 'synced_at']
                );
            }

            if ($labelsStale) {
                UnifiedInventory::where('outlet_id', $outlet->id)->whereIn('item_id', $labelsStale)
                    ->update(['labels' => null]);
            }

            $deactivated = UnifiedInventory::where('outlet_id', $outlet->id)
                ->where('is_active', true)
                ->whereNotIn('item_id', $posItems->pluck('item_id')->all())
                ->update(['is_active' => false, 'synced_at' => $now]);
        });

        return back()->with('success', sprintf(
            'Menu synced from POS: %d new, %d updated, %d no longer sold (marked inactive), %d in total.',
            $added, $updated, $deactivated, count($rows)
        ));
    }

    /**
     * Ask the AI service to label the outlet's active items, then keep a copy of the labels on the rows.
     * Unchanged items are skipped by the service, so re-running is cheap.
     */
    public function generateLabels(Request $request)
    {
        $request->validate(['force' => 'nullable|boolean']);

        $outlet = $this->currentOutlet($request);
        if (!$outlet) {
            return back()->with('error', 'Your account is not attached to an outlet.');
        }

        $items = $this->activeMenu($outlet->id);
        if ($items->isEmpty()) {
            return back()->with('error', 'No active products to label. Use "Sync menu" first.');
        }

        $menu = $items->map(fn ($item) => [
            'item_id'     => (int) $item->item_id,
            'name'        => $item->name,
            'category'    => $item->category,
            'description' => $item->description,
            'price'       => $item->price,
        ])->all();

        set_time_limit(300); // ~20 items per OpenAI call, a full outlet can take a minute

        try {
            $response = Http::timeout(280)->post($this->aiApi() . '/labels/generate', [
                'merchant_id' => (int) $outlet->merchant_id,
                'outlet_id'   => (int) $outlet->id,
                'menu'        => $menu,
                'force'       => $request->boolean('force'),
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not reach the AI service: ' . $e->getMessage());
        }

        if ($response->failed()) {
            return back()->with('error', 'Labelling failed: ' . ($response->json('error') ?? 'AI service error'));
        }

        $result = $response->json();
        $this->storeLabelCopies($outlet, $items->pluck('item_id')->all());

        $message = sprintf(
            'Labelled %d product(s), %d unchanged and skipped.',
            $result['labelled'] ?? 0,
            $result['skipped'] ?? 0
        );
        if (!empty($result['failed'])) {
            $message .= ' ' . count($result['failed']) . ' could not be labelled; run it again to retry them.';
        }

        return back()->with('success', $message);
    }

    /**
     * The menu the recommender reads: active rows of the user's outlet, straight from
     * unified_inventory_table (no joins), with their AI labels. Sent to /recommend by the customer page.
     */
    public function menu(Request $request)
    {
        $outlet = $this->currentOutlet($request);
        if (!$outlet) {
            return response()->json(['error' => 'Your account is not attached to an outlet.'], 404);
        }

        $menu = $this->activeMenu($outlet->id)->map(fn ($item) => array_filter([
            'item_id'     => (int) $item->item_id,
            'name'        => $item->name,
            'category'    => $item->category,
            'description' => $item->description,
            'price'       => $item->price,
            'image_url'   => $item->image_path ? asset('storage/' . $item->image_path) : null,
            'labels'      => $item->labels,
        ], fn ($value) => $value !== null))->values();

        return response()->json([
            'merchant_id' => (int) $outlet->merchant_id,
            'outlet_id'   => (int) $outlet->id,
            'menu'        => $menu,
        ]);
    }

    /** The outlet (with its merchant) the logged-in user manages, or null. */
    private function currentOutlet(Request $request): ?object
    {
        $outletId = $request->user()?->outlet_id;

        return $outletId
            ? DB::table('outlets')->where('id', $outletId)->whereNull('deleted_at')->first(['id', 'merchant_id', 'name'])
            : null;
    }

    private function activeMenu(int $outletId)
    {
        return UnifiedInventory::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->orderBy('item_id')
            ->limit(self::MAX_MENU_ITEMS)
            ->get();
    }

    /**
     * POS side of the sync: one row per item sold at the outlet (variants collapsed).
     * This is the only place that joins the POS tables.
     */
    private function posMenuQuery(int $outletId): Builder
    {
        return DB::table('items as i')
            ->join('item_outlet_map as iom', 'iom.item_id', '=', 'i.id')
            ->where('iom.outlet_id', $outletId)
            ->whereNull('i.deleted_at')
            ->where('i.sellable', true)
            ->selectRaw('i.id AS item_id')
            ->addSelect('i.name', 'i.base_unit_price as price')
            ->selectRaw('i."desc" AS description')
            ->selectRaw('(iom.is_active AND i.is_active) AS is_active')
            ->selectRaw(
                "(SELECT string_agg(c.name, ', ' ORDER BY c.name)
                    FROM item_category_map m
                    JOIN item_categories c ON c.id = m.category_id AND NOT c.is_deleted
                   WHERE m.item_id = i.id) AS category"
            )
            ->selectRaw(
                '(SELECT img.image_path FROM item_images img
                   WHERE img.item_id = i.id AND NOT COALESCE(img.is_deleted, false)
                   ORDER BY img.is_main DESC NULLS LAST, img.id LIMIT 1) AS image_path'
            )
            ->orderBy('i.id');
    }

    /** Copies the label store's labels for these items onto the rows (best effort). */
    private function storeLabelCopies(object $outlet, array $itemIds): void
    {
        try {
            $response = Http::timeout(10)->get($this->aiApi() . '/labels', [
                'merchant_id' => (int) $outlet->merchant_id,
                'item_ids'    => implode(',', $itemIds),
            ]);
            if (!$response->successful()) {
                return;
            }

            foreach (($response->json('labels') ?? []) as $itemId => $labels) {
                DB::table('unified_inventory_table')
                    ->where('outlet_id', $outlet->id)
                    ->where('item_id', (int) $itemId)
                    ->update(['labels' => json_encode($labels, JSON_UNESCAPED_UNICODE)]);
            }
        } catch (\Throwable $e) {
            report($e); // labelling itself succeeded; the copy is refreshed on the next run
        }
    }

    private function aiApi(): string
    {
        return rtrim(env('FLASK_API_INTERNAL', 'http://aicusper-api:5000'), '/');
    }
}
