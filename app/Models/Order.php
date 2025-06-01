<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = ['user_id', 'resevation_id', 'is_paid', 'price'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
    public function discount(): belongsToMany
    {
        return $this->belongsToMany(Discount::class, 'order_discount', 'order_id', 'discount_id');
    }
}
