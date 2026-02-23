<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('/', [RecommendationController::class, 'index']);
Route::post('/recommend', [RecommendationController::class, 'recommend'])->name('recommend');
Route::get('/api/latest-result', function () {
    try {
        $response = Http::get('http://127.0.0.1:5000/latest_result.json');

        if ($response->failed()) {
            return response()->json(['error' => 'API request failed.'], 500);
        }

        return response()->json($response->json());
    } catch (\Exception $e) {
        return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
    }
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::get('/product/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/{id}', [ProductController::class, 'update'])->name('product.update');

});

require __DIR__.'/auth.php';
