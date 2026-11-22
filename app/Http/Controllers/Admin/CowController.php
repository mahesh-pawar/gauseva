<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'Admin: list cows';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return 'Admin: form to add a cow';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return 'Admin: save new cow';
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return "Admin: show cow {$id}";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return "Admin: edit form for cow {$id}";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return "Admin: update cow {$id}";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return "Admin: delete cow {$id}";
    }
}
