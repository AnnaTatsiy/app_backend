<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [

        'surname',
        'name',
        'patronymic',
        'passport',
        'birth',
        'mail',
        'number',
        'registration'
    ];

    // сторона "один" отношения "1:М" - отношение "имеет"
    public function unlimited_subscription():HasMany {
        return $this->hasMany(UnlimitedSubscription::class);
    }

    public function limited_subscription(): HasMany {
        return $this->hasMany(LimitedSubscription::class);
    }

    public function sign_up_group_workout():HasMany {
        return $this->hasMany(SignUpGroupWorkout::class);
    }

    public function sign_up_personal_workout():HasMany {
        return $this->hasMany(SignUpPersonalWorkout::class);
    }
}
