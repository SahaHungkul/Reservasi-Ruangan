<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservations extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'date',
        'day',
        'start_time',
        'end_time',
        'status',
        'reason'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    
    protected $attributes = [
        'status' => 'pending',
    ];
    protected static function booted()
    {
        static::creating(function ($reservation) {
            if (!empty($reservation->date) && !empty($reservation->start_time)) {
                $datetime = Carbon::parse($reservation->date . ' ' . $reservation->start_time);
                $reservation->day = $datetime->translatedFormat('l');
                // ->locale('id')
            }
        });
    }

    public function getStartTimeAttribute()
    {
        return $this->attributes['start_time'] ?? null;
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Rooms::class, 'room_id');
    }
}
