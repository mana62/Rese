<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Restaurant extends Model
{

    protected $fillable = [
        'name',
        'address',
        'area_id',
        'genre_id',
        'description',
        'image',
        'owner_id',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function favorite(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'restaurant_id', 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
