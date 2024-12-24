<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = ['status_id', 'floor'];
    public function status()
    {
        return $this->belongsTo(StatusList::class, 'status_id', 'id');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
