<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // сторона "один" отношения "1:М" - отношение "имеет"
    public function schedule():HasMany {
        return $this->hasMany(Schedule::class);
    }
}

