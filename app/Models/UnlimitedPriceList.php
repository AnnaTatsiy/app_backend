<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnlimitedPriceList extends Model
{
    use HasFactory;

    protected $fillable = [

        'subscription_type_id',
        'validity_period',
        'price'

    ];

    // сторона "много" отношение "1:М", отношение "принадлежит"
    public function subscription_type(): BelongsTo
    {
        return $this->belongsTo(SubscriptionType::class);
    }
}
