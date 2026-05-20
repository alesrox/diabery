<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller {
    public function index() {
        $foods = auth()->user()->foods()->orderBy('name', 'asc')->get();
        return view('foods.index', compact('foods'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'measure_type' => 'required|in:grams,units',
        ]);


        auth()->user()->foods()->create($validated);

        return redirect()->route('foods.index')->with('success', 'Food added!');
    }

    public function destroy(Food $food)
    {
        if ($food->user_id !== auth()->id()) {
            abort(403);
        }

        $food->delete();
        return redirect()->route('foods.index')->with('success', 'Alimento eliminado.');
    }
}
