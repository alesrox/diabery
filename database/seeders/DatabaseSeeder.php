<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Food;
use App\Models\Entry;
use App\Enums\MealType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Alex',
            'email' => 'alex@test.com',
            'password' => Hash::make('1234'),
            'insulin_sensitivity_factor' => 20,
            'carb_insulin_ratio' => 3,
            'grams_per_ration' => 10,
            'target_glucose' => 90,
        ]);

        $pasta = Food::create([
            'user_id' => $user->id,
            'name' => 'Pasta Integral',
            'quantity' => 70,
        ]);

        $manzana = Food::create([
            'user_id' => $user->id,
            'name' => 'Manzana',
            'quantity' => 14,
        ]);

        $pan = Food::create([
            'user_id' => $user->id,
            'name' => 'Pan de Centeno',
            'quantity' => 45,
        ]);

        $oldEntry = Entry::create([
            'user_id' => $user->id,
            'entry_at' => Carbon::now()->subHours(5),
            'meal_type' => MealType::Lunch,
            'glucose_pre' => 140,
            'glucose_post' => 115,
            'meal_bolus' => 6,
            'correction_bolus' => 0.5,
            'total_carbs_sum' => 50,
            'notes' => 'Comida en casa de mi abuela',
        ]);

        $oldEntry->foods()->attach($pasta->id, ['quantity' => 70, 'calculated_carbs' => 49]);

        $recentEntry = Entry::create([
            'user_id' => $user->id,
            'entry_at' => Carbon::now()->subHours(3),
            'meal_type' => MealType::Breakfast,
            'glucose_pre' => 105,
            'meal_bolus' => 4,
            'total_carbs_sum' => 30,
        ]);
        $recentEntry->foods()->attach($pan->id, ['quantity' => 60, 'calculated_carbs' => 27]);
    }
}
