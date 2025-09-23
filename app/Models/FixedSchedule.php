<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedSchedule extends Model
{
    use HasFactory;

    protected $table = 'fixed_schedule';

    protected $fillable = [
        'room_id',
        'day_of_week',
        'start_time',
        'end_time',
        'description',
    ];

    public function room()
    {
        return $this->belongsTo(Rooms::class);
    }
}
