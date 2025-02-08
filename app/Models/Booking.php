<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\{BelongsTo};
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
