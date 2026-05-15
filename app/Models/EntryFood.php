<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryFood extends Model
{
    protected $table = 'entry_food';

    protected $fillable = [
        'entry_id', 'food_id', 'weight_grams', 'calculated_carbs'
    ];
}
