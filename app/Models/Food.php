<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    // app/Models/Food.php
    protected $table = 'foods';
    protected $fillable = ['user_id', 'name', 'quantity', 'measure_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
    {
        return $this->belongsToMany(Entry::class, 'entry_food', 'food_id', 'entry_id')
                    ->withPivot('quantity', 'calculated_carbs')
                    ->withTimestamps();
    }
}
