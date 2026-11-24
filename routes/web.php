<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
/* Route::get('/', function () {
    return view('welcome');
}); */

// the old public webcam page is retired: / goes to the dashboard (which asks for a login first)
Route::get('/', fn () => redirect()->route('dashboard'));
Route::post('/recommend', [RecommendationController::class, 'recommend'])->name('recommend');
Route::get('/api/latest-result', function () {
    try {
        $response = Http::get(env('FLASK_API_INTERNAL', 'http://aicusper-api:5000') . '/latest_result.json');

        if ($response->failed()) {
            return response()->json(['error' => 'API request failed.'], 500);
        }

        return response()->json($response->json());
    } catch (\Exception $e) {
        return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
    }
});

Route::get('/dashboard', function (Request $request) {
    $outlet = $request->user()->outlet_id
        ? \Illuminate\Support\Facades\DB::table('outlets')->where('id', $request->user()->outlet_id)->first(['id', 'merchant_id', 'name'])
        : null;

    $menu = fn () => \App\Models\UnifiedInventory::where('outlet_id', $outlet->id);

    return view('dashboard', [
        'outlet'        => $outlet,
        'activeCount'   => $outlet ? $menu()->where('is_active', true)->count() : null,
        'labelledCount' => $outlet ? $menu()->where('is_active', true)->whereNotNull('labels')->count() : null,
        'lastSynced'    => $outlet ? $menu()->max('synced_at') : null,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::post('/product/sync', [ProductController::class, 'syncMenu'])->name('product.sync');
    Route::post('/product/labels', [ProductController::class, 'generateLabels'])->name('product.labels');
    // the recommender's menu: active rows of the user's outlet from unified_inventory_table
    Route::get('/api/menu', [ProductController::class, 'menu'])->name('api.menu');

    // customer display: needs a login so the outlet is the logged-in user's outlet
    Route::get('/customer', function () {
        return view('customer', ['outletId' => auth()->user()->outlet_id]);
    })->name('customer');
});
Route::get('/cashier', function () {
    return view('remote');
})->name('cashier');
require __DIR__.'/auth.php';


// // Cashier side trigger button POLLING 
// Route::post('/trigger-capture', function () {
//     Cache::put('capture_trigger', true, 30); // valid for 30 sec
//     return response()->json(['status' => 'triggered']);
// });
use App\Events\CaptureTriggered;

Route::post('/trigger-capture', function () {

    \Log::info('route hit');

    event(new CaptureTriggered());

    \Log::info('event dispatched');

    return response()->json(['status' => 'triggered']);
});

// Customer side read cache
Route::get('/check-trigger', function () {
    if (Cache::get('capture_trigger')) {
        Cache::forget('capture_trigger'); // reset after read
        return response()->json(['trigger' => true]);
    }

    return response()->json(['trigger' => false]);
});

// 1️⃣ Remote page POST → create/update trigger file
Route::post('/file-trigger', function (Request $request) {
    Storage::disk('public')->put('trigger.txt', now()->timestamp);
    return response()->json(['ok' => true]);
});

// 2️⃣ Camera page GET → check trigger
Route::get('/file-trigger', function () {
    if (!Storage::disk('public')->exists('trigger.txt')) {
        return response()->json(['triggerTimestamp' => 0]);
    }

    $timestamp = (int) Storage::disk('public')->get('trigger.txt');
    return response()->json(['triggerTimestamp' => $timestamp]);
});

Route::post('/sse-trigger', function (Request $request) {
    // Save a new trigger in cache
    $timestamp = now()->timestamp;
    Cache::put('camera_trigger', $timestamp, 60); // keep for 60 seconds

    return response()->json(['ok' => true, 'timestamp' => $timestamp]);
});

Route::get('/sse-stream', function () {
    return response()->stream(function () {
        while (true) {
            $lastTrigger = Cache::get('camera_trigger', 0);
            echo "data: " . json_encode(['triggerTimestamp' => $lastTrigger]) . "\n\n";
            ob_flush();
            flush();
            sleep(1); // adjust interval if needed
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
});