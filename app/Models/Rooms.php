<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'capacity',
        'description',
        'status'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'inactive',
    ];
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($room) {
            if ($room->isActive()) {
                throw new \Exception('Ruangan aktif tidak dapat dihapus');
            }
        });
    }

    public function reservations()
    {
        return $this->hasMany(Reservations::class, 'room_id');
    }

    public function fixedSchedules()
    {
        return $this->hasMany(FixedSchedule::class, 'room_id');
    }
}
