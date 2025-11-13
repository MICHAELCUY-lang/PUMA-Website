<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cabinet;
use App\Models\Division;
use Illuminate\Support\Facades\DB;

class CabinetDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Refresh and get cabinets
        $kaustav = Cabinet::where('name', 'Kaustav')->first();
        $sapientia = Cabinet::where('name', 'Sapientia')->first();

        if (!$kaustav) {
            $this->command->warn('Kaustav cabinet not found. Creating it now...');
            $kaustav = Cabinet::create([
                'name' => 'Kaustav',
                'description' => 'Kaustav Cabinet - The pioneering leadership team',
                'year' => '2024',
                'status' => 'active',
            ]);
        }

        if (!$sapientia) {
            $this->command->warn('Sapientia cabinet not found. Creating it now...');
            $sapientia = Cabinet::create([
                'name' => 'Sapientia',
                'description' => 'Sapientia Cabinet - Wisdom-driven leadership',
                'year' => '2025',
                'status' => 'active',
            ]);
        }

        // Get divisions by code
        $divisions = [
            'BOD' => Division::where('code', 'BOD')->first(),
            'HRD' => Division::where('code', 'HRD')->first(),
            'ICM' => Division::where('code', 'ICM')->first(),
            'RNT' => Division::where('code', 'RNT')->first(),
            'IR' => Division::where('code', 'IR')->first(),
            'ER' => Division::where('code', 'ER')->first(),
            'SAC' => Division::where('code', 'SAC')->first(),
            'SPT' => Division::where('code', 'SPT')->first(),
            'TECHNOPRENEUR' => Division::where('code', 'TECHNOPRENEUR')->first(),
            'RNC' => Division::where('code', 'RNC')->first(),
            'BD' => Division::where('code', 'BD')->first(),
        ];

        // Kaustav Cabinet divisions
        $kaustavDivisions = [
            ['division' => $divisions['BOD'], 'order' => 1],
            ['division' => $divisions['HRD'], 'order' => 2],
            ['division' => $divisions['ICM'], 'order' => 3],
            ['division' => $divisions['RNT'], 'order' => 4],
            ['division' => $divisions['IR'], 'order' => 5],
            ['division' => $divisions['ER'], 'order' => 6],
            ['division' => $divisions['SAC'], 'order' => 7],
            ['division' => $divisions['SPT'], 'order' => 8],
            ['division' => $divisions['TECHNOPRENEUR'], 'order' => 9],
        ];

        foreach ($kaustavDivisions as $item) {
            if ($item['division']) {
                DB::table('cabinet_divisions')->insert([
                    'cabinet_id' => $kaustav->id,
                    'division_id' => $item['division']->id,
                    'order' => $item['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Sapientia Cabinet divisions - 8 divisions: BOD, RNC, RNT, SAC, HRD, SPT, BD, ICM
        $sapientiaDivisions = [
            ['division' => $divisions['BOD'], 'order' => 1],
            ['division' => $divisions['RNC'], 'order' => 2],
            ['division' => $divisions['RNT'], 'order' => 3],
            ['division' => $divisions['SAC'], 'order' => 4],
            ['division' => $divisions['HRD'], 'order' => 5],
            ['division' => $divisions['SPT'], 'order' => 6],
            ['division' => $divisions['BD'], 'order' => 7],
            ['division' => $divisions['ICM'], 'order' => 8],
        ];

        foreach ($sapientiaDivisions as $item) {
            if ($item['division']) {
                DB::table('cabinet_divisions')->insert([
                    'cabinet_id' => $sapientia->id,
                    'division_id' => $item['division']->id,
                    'order' => $item['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Cabinet divisions seeded successfully!');
    }
}
