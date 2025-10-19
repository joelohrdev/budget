<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,NULL,id,user_id,'.auth()->id()],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        auth()->user()->categories()->create($validated);

        return back();
    }
}
