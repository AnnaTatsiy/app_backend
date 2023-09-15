<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LimitedSubscription extends Model
{
    use HasFactory;

    protected $fillable = [

        'customer_id',
        'limited_price_list_id',
        'open'
    ];

    // сторона "много" отношение "1:М", отношение "принадлежит"
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function limited_price_list(): BelongsTo
    {
        return $this->belongsTo(LimitedPriceList::class, 'limited_price_list_id', 'id');
    }
}
