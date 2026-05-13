<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rooms;
use App\Models\Reservations;
use App\Models\FixedSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Rooms $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->room = Rooms::factory()->create(['status' => 'inactive']);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'room_id'    => $this->room->id,
            'date'       => '2025-06-10',
            'start_time' => '09:00',
            'end_time'   => '10:00',
        ], $override);
    }

    private function postReservation(array $data): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->user)
                    ->postJson('/api/karyawan/reservations', $data);
    }

    // ✅ Berhasil membuat reservasi
    public function test_can_create_reservation(): void
    {
        $this->postReservation($this->payload())
             ->assertStatus(201)
             ->assertJsonPath('data.status', 'pending');
    }

    // ❌ Overlap — booking baru mulai di tengah booking existing
    public function test_rejects_overlapping_reservation(): void
    {
        Reservations::factory()->create([
            'room_id'    => $this->room->id,
            'date'       => '2025-06-10',
            'start_time' => '09:00',
            'end_time'   => '10:00',
            'status'     => 'approved',
        ]);

        $this->postReservation($this->payload(['start_time' => '09:30', 'end_time' => '10:30']))
             ->assertStatus(400);
    }

    // ✅ Booking tepat setelah yang sebelumnya selesai — tidak overlap
    public function test_allows_reservation_right_after_previous_ends(): void
    {
        Reservations::factory()->create([
            'room_id'    => $this->room->id,
            'date'       => '2025-06-10',
            'start_time' => '09:00',
            'end_time'   => '10:00',
            'status'     => 'approved',
        ]);

        $this->postReservation($this->payload(['start_time' => '10:00', 'end_time' => '11:00']))
             ->assertStatus(201);
    }

    // ❌ Bentrok jadwal tetap → harus auto-rejected tapi tersimpan
    public function test_auto_rejects_when_conflicts_with_fixed_schedule(): void
    {
        FixedSchedule::factory()->create([
            'room_id'    => $this->room->id,
            'day_of_week' => 'monday',
            'start_time'  => '08:00',
            'end_time'    => '10:00',
        ]);

        $this->postReservation($this->payload([
                'date'       => '2025-06-09', // Senin
                'start_time' => '08:00',
                'end_time'   => '09:00',
            ]))
             ->assertStatus(400)
             ->assertJsonPath('data.status', 'rejected');

        $this->assertDatabaseHas('reservations', ['status' => 'rejected']);
    }

    // ❌ Ruangan tidak ditemukan
    public function test_returns_404_if_room_not_found(): void
    {
        $this->postReservation($this->payload(['room_id' => 9999]))
             ->assertStatus(404);
    }

    // ✅ Real-time status: ruangan inactive saat tidak ada booking berjalan
    public function test_room_is_inactive_when_no_active_booking(): void
    {
        $status = app(\App\Services\ReservationService::class)
                    ->getRoomRealtimeStatus($this->room->id);

        $this->assertEquals('inactive', $status);
    }

    // ✅ Real-time status: ruangan active saat ada booking berjalan sekarang
    public function test_room_is_active_during_ongoing_booking(): void
    {
        Reservations::factory()->create([
            'room_id'    => $this->room->id,
            'date'       => now()->toDateString(),
            'start_time' => now()->subMinutes(30)->format('H:i:s'),
            'end_time'   => now()->addMinutes(30)->format('H:i:s'),
            'status'     => 'approved',
        ]);

        $status = app(\App\Services\ReservationService::class)
                    ->getRoomRealtimeStatus($this->room->id);

        $this->assertEquals('active', $status);
    }
}
