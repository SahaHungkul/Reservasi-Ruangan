<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'room'       => new RoomResource($this->whenLoaded('room')),
            'user_id'    => $this->user_id,
            'date'       => $this->date,
            'day_of_week'        => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time'   => $this->end_time,
            'status'     => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
