<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UnifiedInventory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UnifiedInventory::query();

        // 🔍 Search by name
        if ($request->filled('name')) {
            $query->where('item_name', 'ILIKE', '%' . $request->name . '%');
        }

        // 📝 Search by description
        if ($request->filled('description')) {
            $query->where('description', 'ILIKE', '%' . $request->description . '%');
        }

        // 🟢🔴 Active status
        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        // 💰 Price
        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }

        $inventory = $query
            ->orderBy('inventory_id', 'desc')
            ->paginate(10)
            ->withQueryString(); // keep filters on pagination

        return view('product.index', compact('inventory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = DB::table('master_outlets')->orderBy('id', 'desc')->get();
        return view('product.form', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $outletsName = DB::table('master_outlets')->where('id', $request->outlet_id)->first();
        $request->validate([
            'outlet_id'   => 'required|integer',
            'outlet_name' => 'nullable|string|max:255',
            'item_name'   => 'nullable|string|max:255',
            'price'       => 'nullable|numeric',
            'description' => 'nullable|string',
            'active'      => 'in:active,inactive',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $imagePath = null;

            // 🔹 Upload image jika ada
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

                $imagePath = $file->storeAs(
                    'inventory',
                    $filename,
                    'public'
                );

                // Jika upload gagal
                if (!$imagePath) {
                    throw new \Exception('Image upload failed');
                }
            }

            // 🔹 Insert ke database
            UnifiedInventory::create([
                'outlet_id'   => $request->outlet_id,
                'outlet_name' => $outletsName->name,
                'item_name'   => $request->item_name,
                'image_path'  => $imagePath,
                'active'      => $request->active ?? 'active',
                'price'       => $request->price,
                'description' => $request->description,
            ]);

            DB::commit();
            
            return Redirect::route('product.index');

        } catch (\Throwable $e) {
            DB::rollBack();

            // optional: hapus file kalau sudah terlanjur upload
            if (isset($imagePath) && $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return response()->json([
                'message' => 'Failed to create inventory',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $inventory = UnifiedInventory::where('inventory_id', $id)->first();
        $outlets = DB::table('master_outlets')->orderBy('id', 'desc')->get();
        return view('product.edit', compact('outlets', 'inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $inventory_id)
    {
        $request->validate([
            'item_name'   => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $inventory = UnifiedInventory::findOrFail($inventory_id);

        DB::beginTransaction();

        try {
            $oldImagePath = $inventory->image_path;
            $newImagePath = $oldImagePath;

            /** ===============================
             * IMAGE PROCESSING
             * =============================== */
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('products', $filename, 'public');

                if (!$path) {
                    throw new \Exception('Image upload failed');
                }

                $newImagePath = $path;
            }

            /** ===============================
             * UPDATE DATABASE
             * =============================== */
            $inventory->update([
                'outlet_id'   => $request->outlet_id,
                'item_name'   => $request->item_name,
                'price'       => $request->price,
                'description' => $request->description,
                'active'      => $request->status,
                'image_path'  => $newImagePath,
            ]);

            /** ===============================
             * DELETE OLD IMAGE (SAFE)
             * =============================== */
            if (
                $request->hasFile('image') &&
                $oldImagePath &&
                Storage::disk('public')->exists($oldImagePath)
            ) {
                Storage::disk('public')->delete($oldImagePath);
            }

            DB::commit();

            return redirect()
                ->route('product.index')
                ->with('success', 'Product updated successfully');

        } catch (\Throwable $e) {
            DB::rollBack();

            // cleanup uploaded image if DB failed
            if (isset($newImagePath) && $newImagePath !== $oldImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update product. ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
