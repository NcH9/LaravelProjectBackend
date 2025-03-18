<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;
    protected $fillable = ['status_id', 'floor'];
    public function status():BelongsTo
    {
        return $this->belongsTo(StatusList::class, 'status_id', 'id');
    }
    public function reservations():HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
