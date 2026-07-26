<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        Driver::insert([
            ['name' => 'Budi Santoso', 'vehicle' => 'Motor - B 1234 XYZ', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Adi Wijaya', 'vehicle' => 'Motor - B 5678 ABC', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rian Pratama', 'vehicle' => 'Mobil Box - B 9012 DEF', 'status' => 'Sibuk', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}