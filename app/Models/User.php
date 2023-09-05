<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory ,HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image_id',
        'role'
    ];

    protected $hidden = [
        'image_id',
        'password'
    ];

    public function customer():HasMany {
        return $this->hasMany(Customer::class);
    }

    public function coach():HasMany {
        return $this->hasMany(Coach::class);
    }

    public function image(): BelongsTo {
        return $this->belongsTo(Image::class);
    }

}
