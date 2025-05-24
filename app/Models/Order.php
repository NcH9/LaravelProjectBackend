<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['user_id', 'resevation_id'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function orderItems()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
    public function reservations(): HasMany {
        return $this->hasMany(Reservation::class, 'reservation_id', 'id');
    }
}
