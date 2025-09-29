<?php

namespace Database\Seeders;

use App\Models\Rooms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Ruang Rapat Umum',
                'capacity' => '20',
                'description' => '',
            ],
            [
                'name' => 'Ruang Rapat',
                'capacity' => '20',
                'description' => '',
            ],
            [
                'name' => 'Ruang Rapat',
                'capacity' => '20',
                'description' => '',
            ],
        ];

        foreach ($rooms as $room){
            Rooms::create([
                'name' => $room['name'],
                'capacity' => $room['capacity'],
                'description' => $room['description'],
                'status' => 'inactive',
            ]);
        }
    }
}
