<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    //
    use HasFactory;
    protected $fillable = ['reservation_start', 'reservation_end', 'room_id', 'user_id', 'price'];
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
