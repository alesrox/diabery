<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // app/Models/User.php

    protected $fillable = [
        'name',
        'email',
        'password',
        'insulin_sensitivity_factor',
        'carb_insulin_ratio',
        'grams_per_ration',
        'target_glucose',
    ];

    // Relación: Un usuario tiene muchas entradas (comidas)
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    // Relación: Un usuario tiene sus propios alimentos guardados
    public function foods()
    {
        return $this->hasMany(Food::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
