<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'percent', 'is_seasonal', 'loyalty_reward'];
    public function users():belongsToMany
    {
        return $this->belongsToMany(User::class, 'discount_user', 'discount_id', 'user_id');
    }
    public function orders():belongsToMany
    {
        return $this->belongsToMany(Order::class, 'discount_order', 'discount_id', 'order_id');
    }
}
