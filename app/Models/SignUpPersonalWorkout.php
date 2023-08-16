<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignUpPersonalWorkout extends Model
{
    use HasFactory;

    protected $fillable = [

        'date_begin',
        'time_begin',
        'coach_id',
        'customer_id'

    ];

    // сторона "много" отношение "1:М", отношение "принадлежит"
    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function coach(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Coach::class);
    }
}
