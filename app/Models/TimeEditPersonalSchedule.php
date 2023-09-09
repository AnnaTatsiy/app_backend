<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEditPersonalSchedule extends Model
{
    protected $fillable = [

        'date_edit',
        'coach_id',
    ];

    public function coach(): BelongsTo {
        return $this->belongsTo(Coach::class);
    }
}
