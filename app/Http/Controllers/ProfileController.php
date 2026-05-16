<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('settings'); // Asegúrate de guardar la vista como settings.blade.php
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        // Validación estricta
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'carb_insulin_ratio' => ['required', 'numeric', 'min:0.1'],
            'insulin_sensitivity_factor' => ['required', 'numeric', 'min:0.1'],
            'target_glucose' => ['required', 'integer', 'min:40', 'max:300'],
        ]);

        // Actualizar datos médicos y email
        $user->email = $validated['email'];
        $user->carb_insulin_ratio = $validated['carb_insulin_ratio'];
        $user->insulin_sensitivity_factor = $validated['insulin_sensitivity_factor'];
        $user->target_glucose = $validated['target_glucose'];

        // Solo cambiar la contraseña si ha escrito una nueva
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('settings.edit')->with('success', '¡Ajustes guardados correctamente!');
    }
}