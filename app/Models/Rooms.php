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

    protected $attributes = [
        'status' => 'inactive',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservations::class,'room_id');
    }

    public function fixedSchedules()
    {
        return $this->hasMany(FixedSchedule::class);
    }
}
