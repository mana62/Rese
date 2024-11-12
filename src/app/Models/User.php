<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    public function favorites()
    {
        return $this->belongsToMany(Restaurant::class, 'favorites');
    }


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function restaurant()
    {
        return $this->hasOne(Restaurant::class, 'owner_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
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
