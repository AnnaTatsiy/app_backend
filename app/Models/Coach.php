<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
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
        'user_id',
        'registration',
        'sale'
    ];

    // сторона "один" отношения "1:М" - отношение "имеет"
    public function schedule():HasMany {
        return $this->hasMany(Schedule::class);
    }

    public function sign_up_personal_workout():HasMany {
        return $this->hasMany(SignUpPersonalWorkout::class);
    }

    public function limited_price_list():HasMany {
        return $this->hasMany(LimitedPriceList::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
