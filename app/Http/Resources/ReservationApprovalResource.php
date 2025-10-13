<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationApprovalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reservation_id' => $this->id,
            'status' => $this->status,
            'room'=>[
                'id' => $this->room->id,
                'name' => $this->room->name,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'reason' => $this->when(!is_null($this->reason), $this->reason),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'updated_at'=> $this->updated_at,
        ];
    }
}
