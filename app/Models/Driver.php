<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'status',
        'last_latitude',
        'last_longitude',
        'last_location_at',
    ];

    protected function casts(): array
    {
        return [
            'last_latitude' => 'decimal:7',
            'last_longitude' => 'decimal:7',
            'last_location_at' => 'datetime',
        ];
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
