<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'room_id'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function orderItems()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
