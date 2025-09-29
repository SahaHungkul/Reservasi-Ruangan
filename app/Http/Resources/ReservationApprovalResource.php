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
            'room_id'     => $this->room_id,
            'user_id'     => $this->user_id,
            'status'      => $this->status,
            // 'reason' => $this->when(
            //     in_array($this->status, ['rejected', 'cancel']),
            //     $this->reason
            // ),

            'reason'     => $this->when(
                $this->status === 'approved' && !is_null($this->reason),
                $this->reason
            ),

            // 'reason'     => $this->when(
            //     in_array($this->status, ['cancel', 'rejected']),
            //     $this->reason
            // ),

            // 'reason' => $this->reason,
            'start_time'  => $this->start_time,
            'end_time'    => $this->end_time,
            'updated_at'  => $this->updated_at,
        ];
    }
}
