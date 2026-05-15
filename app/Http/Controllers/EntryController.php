<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntryController extends Controller {
    public function index(Request $request) {
        $query = auth()->user()->entries()->with('foods');

        if ($request->has('meal_type') && $request->meal_type != '') {
            $query->where('meal_type', $request->meal_type);
        }

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('notes', 'like', "%{$searchTerm}%")
                ->orWhereHas('foods', function($f) use ($searchTerm) {
                    $f->where('name', 'like', "%{$searchTerm}%");
                });
            });
        }

        $entries = $query->orderBy('entry_at', 'desc')->get();

        return view('entries.index', compact('entries'));
    }

    public function create() {
        $foods = auth()->user()->foods()->orderBy('name')->get();
        return view('entries.create', compact('foods'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'meal_type' => 'required|string',
            'glucose_pre' => 'nullable|numeric',
            'meal_bolus' => 'nullable|numeric',
            'correction_bolus' => 'nullable|numeric',
            'total_carbs_sum' => 'required|numeric',
            'notes' => 'nullable|string',
            'foods' => 'nullable|array', 
        ]);

        $entry = Auth::user()->entries()->create([
            'meal_type' => $validated['meal_type'],
            'glucose_pre' => $validated['glucose_pre'] ?? 0,
            'meal_bolus' => $validated['meal_bolus'] ?? 0,
            'correction_bolus' => $validated['correction_bolus'] ?? 0,
            'total_carbs_sum' => $validated['total_carbs_sum'],
            'notes' => $validated['notes'],
            'entry_at' => now(),
        ]);

        if ($request->has('foods')) {
            $pivotData = [];
            foreach ($request->foods as $foodId => $data) {
                $pivotData[$foodId] = [
                    'weight_grams' => $data['weight_grams'],
                    'calculated_carbs' => $data['calculated_carbs'],
                ];
            }

            $entry->foods()->attach($pivotData);
        }

        return redirect()->route('dashboard')->with('success', 'Entrada registrada correctamente.');
    }

    public function edit(Entry $entry) {
        $foods = auth()->user()->foods()->orderBy('name')->get();
        $entry->load('foods'); 
        
        return view('entries.edit', compact('entry', 'foods'));
    }

    public function update(Request $request, Entry $entry) {
        $validated = $request->validate([
            'glucose_pre' => 'nullable|numeric',
            'glucose_post' => 'nullable|numeric',
            'meal_bolus' => 'nullable|numeric',
            'correction_bolus' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $entry->update($validated);
        return redirect()->route('dashboard')->with('success', 'Registro actualizado');
    }
}
