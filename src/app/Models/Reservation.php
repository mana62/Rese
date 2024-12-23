<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Reservation extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'date',
        'time',
        'guests',
        'qr_code',
        'status',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';

    protected $casts = [
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }


    public function getFormattedTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->time)->format('H:i');
    }
}
