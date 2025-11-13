<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cabinet;

class CabinetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cabinet::create([
            'name' => 'Kaustav',
            'description' => 'Kaustav Cabinet - The pioneering leadership team',
            'year' => '2024',
            'status' => 'active',
        ]);

        Cabinet::create([
            'name' => 'Sapientia',
            'description' => 'Sapientia Cabinet - Wisdom-driven leadership',
            'year' => '2025',
            'status' => 'active',
        ]);
    }
}
