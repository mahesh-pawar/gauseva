<?php

use App\Http\Controllers\Admin\CowController as AdminCowController;
use App\Http\Controllers\CowController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/hello', function () {
    return 'Jai Gau Mata! Laravel is working.';
});

Route::view('/about', 'about')->name('about');

Route::resource('cows', CowController::class)->only(['index', 'show']);

Route::get('/products/{category?}', function (string $category = 'all') {
    return "Showing products in: {$category}";
})->name('products.index');

Route::get('orders/{id}', function (int $id) {
    return "Order #{$id}";
})->whereNumber('id')->name('orders.show');

Route::redirect('/home', '/');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => 'Admin Dashboard')->name('dashboard');
    Route::resource('cows', AdminCowController::class);
});

Route::fallback(function () {
    return response('<h1>404 — Page not found</h1><a href="/">Go home</a>', 404);
});
