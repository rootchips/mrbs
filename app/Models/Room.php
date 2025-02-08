<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'equipments' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('rooms');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
