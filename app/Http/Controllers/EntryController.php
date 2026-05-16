<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

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
            'entry_at' => 'required|date',
            'meal_type' => 'required|string',
            'glucose_pre' => 'nullable|numeric',
            'meal_bolus' => 'nullable|numeric',
            'correction_bolus' => 'nullable|numeric',
            'total_carbs_sum' => 'required|numeric',
            'notes' => 'nullable|string',
            'foods' => 'nullable|array', 
        ]);

        $userTimezone = Cookie::get('timezone', 'Europe/Madrid');
        $localDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->entry_at, $userTimezone);

        $entry = Auth::user()->entries()->create([
            'entry_at' => $localDate->setTimezone('UTC'),
            'meal_type' => $validated['meal_type'],
            'glucose_pre' => $validated['glucose_pre'] ?? 0,
            'meal_bolus' => $validated['meal_bolus'] ?? 0,
            'correction_bolus' => $validated['correction_bolus'] ?? 0,
            'total_carbs_sum' => $validated['total_carbs_sum'],
            'notes' => $validated['notes'],
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
            'entry_at' => 'required|date',
            'meal_type' => 'required',
            'glucose_pre' => 'nullable|numeric',
            'glucose_post' => 'nullable|numeric',
            'meal_bolus' => 'nullable|numeric',
            'correction_bolus' => 'nullable|numeric',
            'total_carbs_sum' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $userTimezone = Cookie::get('timezone', 'Europe/Madrid'); 
        $localDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->entry_at, $userTimezone);
        $validated['entry_at'] = $localDate->setTimezone('UTC');

        $validated['glucose_pre'] = $request->filled('glucose_pre') ? $request->glucose_pre : null;
        $validated['glucose_post'] = $request->filled('glucose_post') ? $request->glucose_post : null;
        $validated['meal_bolus'] = $request->filled('meal_bolus') ? $request->meal_bolus : null;
        $validated['correction_bolus'] = $request->filled('correction_bolus') ? $request->correction_bolus : null;

        $entry->update($validated);
        $foodsData = $request->input('foods', []);
        $entry->foods()->sync($foodsData);
        return redirect()->route('dashboard')->with('success', 'Registro actualizado con éxito');
    }

    public function destroy(Entry $entry) {
        $entry->foods()->detach();
        $entry->delete();
        return redirect()->route('dashboard')->with('success', __('Entry deleted successfully'));
    }
}
