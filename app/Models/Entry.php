<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\MealType;

class Entry extends Model
{
    protected $table = 'entries';
    protected $fillable = [
        'user_id', 'entry_at', 'meal_type', 'glucose_pre', 
        'glucose_post', 'meal_bolus', 'correction_bolus', 
        'total_carbs_sum', 'notes', 'suggested_adjustment'
    ];

    protected $casts = [
        'entry_at' => 'datetime',
        'meal_type' => MealType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación Muchos a Muchos con Food
    public function foods()
    {
        return $this->belongsToMany(Food::class, 'entry_food', 'entry_id', 'food_id')
                    ->withPivot('weight_grams', 'calculated_carbs')
                    ->withTimestamps();
    }
}
