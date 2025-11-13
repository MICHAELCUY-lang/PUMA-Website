<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aspiration;
use App\Models\User;

class AspirationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        $aspirations = [
            [
                'user_id' => $user->id,
                'content' => 'Saya ingin PUMA mengadakan lebih banyak event yang interaktif dan menyenangkan, seperti hackathon atau seminar dengan alumni sukses.',
                'type' => 'aspiration',
                'status' => 'reviewed',
                'response' => 'Terima kasih atas sarannya! Kami sedang merencanakan hackathon untuk semester depan.',
                'created_at' => now()->subDays(10),
            ],
            [
                'user_id' => $user->id,
                'content' => 'Aspirasi saya adalah melihat website organisasi kita tampil lebih modern dan responsif. Mungkin kita bisa pakai Tailwind CSS atau redesign total tampilan dashboard-nya.',
                'type' => 'suggestion',
                'status' => 'implemented',
                'response' => 'Website baru sudah diluncurkan dengan Tailwind CSS!',
                'created_at' => now()->subDays(15),
            ],
            [
                'user_id' => $user->id,
                'content' => 'Saya harap PUMA bisa lebih aktif dalam mendengarkan dan menyalurkan aspirasi mahasiswa ke pihak kampus, terutama soal fasilitas kelas dan WiFi.',
                'type' => 'aspiration',
                'status' => 'new',
                'response' => null,
                'created_at' => now()->subDays(5),
            ],
            [
                'user_id' => $user->id,
                'content' => 'Aspirasi saya adalah ada lebih banyak kegiatan lintas jurusan agar mahasiswa bisa saling kenal dan kolaborasi antar program studi.',
                'type' => 'aspiration',
                'status' => 'reviewed',
                'response' => 'Kami sedang mengkoordinasikan event lintas jurusan bersama organisasi lain.',
                'created_at' => now()->subDays(8),
            ],
            [
                'user_id' => $user->id,
                'content' => 'Saya ingin melihat PUMA memiliki divisi kreatif yang lebih aktif memproduksi konten visual, desain feed IG, dan dokumentasi event dengan standar profesional.',
                'type' => 'suggestion',
                'status' => 'reviewed',
                'response' => 'Divisi ICM sedang meningkatkan kualitas konten visual kami.',
                'created_at' => now()->subDays(12),
            ],
            [
                'user_id' => $user->id,
                'content' => 'Harapanku adalah sistem komunikasi internal PUMA lebih rapi, misalnya dengan pakai Notion atau platform khusus buat koordinasi tim.',
                'type' => 'suggestion',
                'status' => 'implemented',
                'response' => 'Kami sudah mengimplementasikan Notion untuk koordinasi internal!',
                'created_at' => now()->subDays(20),
            ],
            [
                'user_id' => $user->id,
                'content' => 'Saya ingin lebih banyak pelatihan soft skill seperti public speaking atau kepemimpinan dalam program kerja PUMA.',
                'type' => 'aspiration',
                'status' => 'new',
                'response' => null,
                'created_at' => now()->subDays(3),
            ],
            [
                'user_id' => $user->id,
                'content' => 'Aspirasi saya adalah adanya dokumentasi dan publikasi rutin tentang progres dan laporan kerja PUMA agar lebih transparan.',
                'type' => 'feedback',
                'status' => 'reviewed',
                'response' => 'Kami akan mulai membuat laporan bulanan di website.',
                'created_at' => now()->subDays(7),
            ],
        ];

        foreach ($aspirations as $aspiration) {
            Aspiration::create($aspiration);
        }

        $this->command->info('Aspirations seeded successfully!');
    }
}
