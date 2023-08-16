<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gym extends Model
{
    use HasFactory;

    protected $fillable = [

        'title',
        'capacity'
    ];

    // сторона "один" отношения "1:М" - отношение "имеет"
    public function schedule():HasMany {
        return $this->hasMany(Schedule::class);
    }
}
