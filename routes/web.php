<?php

use Illuminate\Support\Facades\Route;

$cows = [
    'gauri' => ['name' => 'Gauri',  'breed' => 'Gir',     'age' => 5],
    'kamdhenu' => ['name' => 'Kamdhenu', 'breed' => 'Sahiwal', 'age' => 7],
    'nandini' => ['name' => 'Nandini', 'breed' => 'Tharparkar', 'age' => 3],
];

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return 'Jai Gau Mata! Laravel is working.';
});

Route::view('/about', 'about')->name('about');

Route::get('/cows', function () use ($cows) {
    $html = '<h1>Our Cows</h1>';
    foreach ($cows as $slug => $cow) {
        $html .= '<p><a href="'.route('cows.show', $slug).'">'.$cow['name'].'</a></p>';
    }

    return $html;
})->name('cows.index');

Route::get('/cows/{slug}', function (string $slug) use ($cows) {
    if (! isset($cows[$slug])) {
        abort(404);
    }
    $cow = $cows[$slug];

    return "<h1>{$cow['name']}</h1><p>Breed: {$cow['breed']}, Age: {$cow['age']}</p>"
        .'<a href="'.route('cows.index').'">← All cows</a>';
})->name('cows.show');

Route::get('/products/{category?}', function (string $category = 'all') {
    return "Showing products in: {$category}";
})->name('products.index');

Route::get('orders/{id}', function (int $id) {
    return "Order #{$id}";
})->whereNumber('id')->name('orders.show');
