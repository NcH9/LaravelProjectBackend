<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'rooms';
    protected $fillable = ['status', 'floor', 'number', 'price'];
    public function reservations():HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
