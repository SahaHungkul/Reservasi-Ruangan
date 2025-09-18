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

    public function reservations()
    {
        return $this->hasMany(Reservations::class);
    }

    public function fixedSchedules()
    {
        return $this->hasMany(FixedSchedule::class);
    }
}
