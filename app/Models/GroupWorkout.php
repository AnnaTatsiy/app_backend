<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupWorkout extends Model
{
    use HasFactory;

    protected $fillable = [

        'event',
        'cancelled',
        'schedule_id',
        'reason'
    ];

    // сторона "много" отношение "1:М", отношение "принадлежит"
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
