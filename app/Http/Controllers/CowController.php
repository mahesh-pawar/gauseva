<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CowController extends Controller
{
    private array $cows = [
        'gauri' => ['name' => 'Gauri',  'breed' => 'Gir',     'age' => 5],
        'kamdhenu' => ['name' => 'Kamdhenu', 'breed' => 'Sahiwal', 'age' => 7],
        'nandini' => ['name' => 'Nandini', 'breed' => 'Tharparkar', 'age' => 3],
    ];

    public function index()
    {
        $html = '<h1>Our Cows</h1>';
        foreach ($this->cows as $slug => $cow) {
            $html .= '<p><a href="'.route('cows.show', $slug).'">'.$cow['name'].'</a></p>';
        }

        return $html;
    }

    public function show(Request $request, string $cow)
    {
        if (! isset($this->cows[$cow])) {
            abort(404);
        }
        $cow = $this->cows[$cow];

        $lang = $request->query('lang', 'en');

        return "<h1>{$cow['name']}</h1><p>Breed: {$cow['breed']}, Age: {$cow['age']}</p><p>Language: {$lang}</p>"
            .'<a href="'.route('cows.index').'">← All cows</a>';
    }
}
