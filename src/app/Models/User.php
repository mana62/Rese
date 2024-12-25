<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = ['name', 'email', 'password', 'role', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'favorites', 'user_id', 'restaurant_id');
    }


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function restaurant()
    {
        return $this->hasOne(Restaurant::class, 'owner_id');
    }

    public function isStoreOwner()
    {
        return $this->role === 'store-owner';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
