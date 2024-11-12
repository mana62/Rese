<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'users';

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}