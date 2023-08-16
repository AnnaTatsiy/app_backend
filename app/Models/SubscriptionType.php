<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionType extends Model
{
    use HasFactory;

    protected $fillable = [

        'title',
        'spa',
        'pool',
        'group'
    ];


    // сторона "один" отношения "1:М" - отношение "имеет"
    public function unlimited_price_list():HasMany {
        return $this->hasMany(UnlimitedPriceList::class);
    }
}
