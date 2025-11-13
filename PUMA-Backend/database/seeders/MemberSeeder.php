<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use App\Models\Division;
use App\Models\Cabinet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get cabinets
        $kaustav = Cabinet::where('name', 'like', '%Kaustav%')->first();

        // Get divisions
        $divisions = [
            'BOD' => Division::where('name', 'BOD')->first(),
            'ER' => Division::where('name', 'ER')->first(),
            'IR' => Division::where('name', 'IR')->first(),
            'SAC' => Division::where('name', 'SAC')->first(),
            'ICM' => Division::where('name', 'ICM')->first(),
            'RNT' => Division::where('name', 'RNT')->first(),
            'TECHNOPRENEUR' => Division::where('name', 'TECHNOPRENEUR')->first(),
            'HRD' => Division::where('name', 'HRD')->first(),
            'SPT' => Division::where('name', 'SPT')->first(),
        ];

        $members = [
            // BOD Members
            ['name' => 'Abdurrahman Khairi', 'position' => 'Chairperson', 'batch' => '2023', 'division' => 'BOD', 'avatar' => '/PUMA-Website/khairi.JPG', 'status' => 'inactive'],
            ['name' => 'Filbert Sembiring Meliala', 'position' => 'Vice Chairperson 1', 'batch' => '2023', 'division' => 'BOD', 'avatar' => '/PUMA-Website/filbert.JPG', 'status' => 'inactive'],
            ['name' => 'Leonardo Dos Santos', 'position' => 'Vice Chairperson 2', 'batch' => '2023', 'division' => 'BOD', 'avatar' => '/PUMA-Website/leonardo.JPG', 'status' => 'inactive'],
            ['name' => 'Moshe Dayan', 'position' => 'Senior Treasurer', 'batch' => '2023', 'division' => 'BOD', 'avatar' => '/PUMA-Website/moshe.JPG', 'status' => 'inactive'],
            ['name' => 'Elvia Aptanisa', 'position' => 'Junior Treasurer', 'batch' => '2024', 'division' => 'BOD', 'avatar' => '/PUMA-Website/elvi.JPG', 'status' => 'active'],
            ['name' => 'Michelle', 'position' => 'Junior Treasurer', 'batch' => '2024', 'division' => 'BOD', 'avatar' => '/PUMA-Website/michelle.JPG', 'status' => 'active'],
            ['name' => 'Desy Nursalsabila', 'position' => 'Senior Secretary', 'batch' => '2023', 'division' => 'BOD', 'avatar' => '/PUMA-Website/desy.JPG', 'status' => 'inactive'],
            ['name' => 'Putri Zahara', 'position' => 'Junior Secretary', 'batch' => '2024', 'division' => 'BOD', 'avatar' => '/PUMA-Website/putri.JPG', 'status' => 'active'],
            ['name' => 'Zuldan Fahrizal Rahman', 'position' => 'Junior Secretary', 'batch' => '2024', 'division' => 'BOD', 'avatar' => '/PUMA-Website/zuldan.JPG', 'status' => 'active'],

            // ER Members
            ['name' => 'Isya Maghfira Zalfa', 'position' => 'Head of External Relation', 'batch' => '2023', 'division' => 'ER', 'avatar' => '/PUMA-Website/cica.JPG', 'status' => 'inactive'],
            ['name' => 'Sarahwati', 'position' => 'Vice of External Relation', 'batch' => '2023', 'division' => 'ER', 'avatar' => '/PUMA-Website/sarah.JPG', 'status' => 'inactive'],
            ['name' => 'Shanty', 'position' => 'Member of External Relation', 'batch' => '2024', 'division' => 'ER', 'avatar' => '/PUMA-Website/shanty.JPG', 'status' => 'active'],
            ['name' => 'Naufal Rizki Pinugroho', 'position' => 'Member of External Relation', 'batch' => '2024', 'division' => 'ER', 'avatar' => '/PUMA-Website/nopal.JPG', 'status' => 'active'],
            ['name' => 'Made Mas Pradnya Prabawa', 'position' => 'Member of External Relation', 'batch' => '2024', 'division' => 'ER', 'avatar' => '/PUMA-Website/prad.JPG', 'status' => 'active'],
            ['name' => 'Gideon Anggara Siagian', 'position' => 'Member of External Relation', 'batch' => '2024', 'division' => 'ER', 'avatar' => '/PUMA-Website/gideon.JPG', 'status' => 'active'],

            // IR Members
            ['name' => 'Bianca Vallerie', 'position' => 'Head of Internal Relation', 'batch' => '2023', 'division' => 'IR', 'avatar' => '/PUMA-Website/bianca.JPG', 'status' => 'inactive'],
            ['name' => 'Abigail Tiara Larasati', 'position' => 'Vice of Internal Relation', 'batch' => '2023', 'division' => 'IR', 'avatar' => '/PUMA-Website/bigel.JPG', 'status' => 'inactive'],
            ['name' => 'Johana Veronica Setiawan', 'position' => 'Member of Internal Relation', 'batch' => '2024', 'division' => 'IR', 'avatar' => '/PUMA-Website/joana.JPG', 'status' => 'active'],
            ['name' => 'Muhammad Dzaki Abrar', 'position' => 'Member of Internal Relation', 'batch' => '2024', 'division' => 'IR', 'avatar' => '/PUMA-Website/dzaki.JPG', 'status' => 'active'],
            ['name' => 'Zain Akbar', 'position' => 'Member of Internal Relation', 'batch' => '2024', 'division' => 'IR', 'avatar' => '/PUMA-Website/akbar.JPG', 'status' => 'active'],

            // SAC Members
            ['name' => 'Hana Khairunnisa Nabiilah', 'position' => 'Head of Student Academic and Competition', 'batch' => '2023', 'division' => 'SAC', 'avatar' => '/PUMA-Website/hana.JPG', 'status' => 'inactive'],
            ['name' => 'Sarah Kimberly Fischer', 'position' => 'Vice of Student Academy and Competition', 'batch' => '2023', 'division' => 'SAC', 'avatar' => '/PUMA-Website/kim.JPG', 'status' => 'inactive'],
            ['name' => 'Lutfi Maulana', 'position' => 'Member of Student Academy and Competition', 'batch' => '2024', 'division' => 'SAC', 'avatar' => '/PUMA-Website/lutfi.JPG', 'status' => 'active'],
            ['name' => 'Wilbert Leonard Harriman', 'position' => 'Member of Student Academy and Competition', 'batch' => '2024', 'division' => 'SAC', 'avatar' => '/PUMA-Website/wilbert.JPG', 'status' => 'active'],
            ['name' => 'Yasmin Raihanah Inayudha', 'position' => 'Member of Student Academy and Competition', 'batch' => '2024', 'division' => 'SAC', 'avatar' => '/PUMA-Website/yasmin.JPG', 'status' => 'active'],
            ['name' => 'Cut Kheysa Sakbania', 'position' => 'Member of Student Academy and Competition', 'batch' => '2024', 'division' => 'SAC', 'avatar' => '/PUMA-Website/cut.JPG', 'status' => 'active'],

            // ICM Members
            ['name' => 'Made Mahatti Prayascita Chandra', 'position' => 'Head of Information and Creative Media', 'batch' => '2023', 'division' => 'ICM', 'avatar' => '/PUMA-Website/mahatti.JPG', 'status' => 'inactive'],
            ['name' => 'Nadifah Aulia Rahmani', 'position' => 'Vice of Media Information', 'batch' => '2023', 'division' => 'ICM', 'avatar' => '/PUMA-Website/nadifah.JPG', 'status' => 'inactive'],
            ['name' => 'Muhammad Afdal Fikri', 'position' => 'Member of Media Information', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/afdal.JPG', 'status' => 'active'],
            ['name' => 'Naila Olivia', 'position' => 'Member of Media Information', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/olivia.JPG', 'status' => 'active'],
            ['name' => 'Azqa Difani Akbar', 'position' => 'Vice of Creative Media', 'batch' => '2023', 'division' => 'ICM', 'avatar' => '/PUMA-Website/azka.JPG', 'status' => 'inactive'],
            ['name' => 'Gamma Ahmad Zaki Kurnia Budihardjo', 'position' => 'Member of Creative Media', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/gamma.JPG', 'status' => 'active'],
            ['name' => 'Almira Shinta Aulia', 'position' => 'Member of Creative Media', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/almira.JPG', 'status' => 'active'],
            ['name' => 'Richie Obasa', 'position' => 'Member of Creative Media', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/richie.JPG', 'status' => 'active'],
            ['name' => 'Gabriel Hamonangan Lumban Tobing', 'position' => 'Member of Creative Media', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/hamongan.JPG', 'status' => 'active'],
            ['name' => 'Raisya Eka Putri', 'position' => 'Member of Creative Media', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/raisya.JPG', 'status' => 'active'],
            ['name' => 'Kevin Syonin', 'position' => 'Member of Creative Media', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/kevin.JPG', 'status' => 'active'],
            ['name' => 'Dewa Anggara Satria Pratama', 'position' => 'Member of Creative Media', 'batch' => '2024', 'division' => 'ICM', 'avatar' => '/PUMA-Website/dewa.JPG', 'status' => 'active'],

            // RNT Members
            ['name' => 'Rix Valdo', 'position' => 'Head of Research and Technology', 'batch' => '2023', 'division' => 'RNT', 'avatar' => '/PUMA-Website/rix.jpg', 'status' => 'inactive'],
            ['name' => 'Jason Anthony Wibowo', 'position' => 'Vice of Research and Technology', 'batch' => '2023', 'division' => 'RNT', 'avatar' => '/PUMA-Website/jason.jpg', 'status' => 'inactive'],
            ['name' => 'Muhammad Haikal Islami', 'position' => 'Member of Research and Technology', 'batch' => '2023', 'division' => 'RNT', 'avatar' => '/PUMA-Website/haikal.jpg', 'status' => 'inactive'],
            ['name' => 'Briant Jasper', 'position' => 'Member of Research and Technology', 'batch' => '2024', 'division' => 'RNT', 'avatar' => '/PUMA-Website/briant.jpg', 'status' => 'active'],
            ['name' => 'Keira Nevrada Lay', 'position' => 'Member of Research and Technology', 'batch' => '2024', 'division' => 'RNT', 'avatar' => '/PUMA-Website/keira.jpg', 'status' => 'active'],
            ['name' => 'Janet Dewi Evangeline', 'position' => 'Member of Research and Technology', 'batch' => '2024', 'division' => 'RNT', 'avatar' => '/PUMA-Website/jane.jpg', 'status' => 'active'],
            ['name' => 'Nisrina Izza Nur Aisyah', 'position' => 'Member of Research and Technology', 'batch' => '2024', 'division' => 'RNT', 'avatar' => '/PUMA-Website/nina.jpg', 'status' => 'active'],

            // TECHNOPRENEUR Members
            ['name' => 'Ubaidillah Al-Azhar', 'position' => 'Head of Technopreneur', 'batch' => '2023', 'division' => 'TECHNOPRENEUR', 'avatar' => '/PUMA-Website/ubai.JPG', 'status' => 'inactive'],
            ['name' => 'Salsa Ica Indriani', 'position' => 'Vice of Technopreneur', 'batch' => '2023', 'division' => 'TECHNOPRENEUR', 'avatar' => '/PUMA-Website/ica.JPG', 'status' => 'inactive'],
            ['name' => 'Navisa Ersa Sabina', 'position' => 'Member of Technopreneur', 'batch' => '2024', 'division' => 'TECHNOPRENEUR', 'avatar' => '/PUMA-Website/sasa.JPG', 'status' => 'active'],
            ['name' => 'Michael Bryan Mandey', 'position' => 'Member of Technopreneur', 'batch' => '2024', 'division' => 'TECHNOPRENEUR', 'avatar' => '/PUMA-Website/michael.JPG', 'status' => 'active'],
            ['name' => 'Nailha Sakhila Dewi', 'position' => 'Member of Technopreneur', 'batch' => '2024', 'division' => 'TECHNOPRENEUR', 'avatar' => '/PUMA-Website/nailha.JPG', 'status' => 'active'],

            // HRD Members
            ['name' => 'Joy Adelia Sihombing', 'position' => 'Head of Human Resources Development', 'batch' => '2023', 'division' => 'HRD', 'avatar' => '/PUMA-Website/joy.JPG', 'status' => 'inactive'],
            ['name' => 'Intan Kumala Pasya', 'position' => 'Vice of Human Resources Development', 'batch' => '2023', 'division' => 'HRD', 'avatar' => '/PUMA-Website/intan.JPG', 'status' => 'inactive'],
            ['name' => 'Tio Muhammad Rizky', 'position' => 'Member of Human Resources Development', 'batch' => '2024', 'division' => 'HRD', 'avatar' => '/PUMA-Website/tio.JPG', 'status' => 'active'],
            ['name' => 'Angelina Yolanda Christin Lubis', 'position' => 'Member of Human Resources Development', 'batch' => '2024', 'division' => 'HRD', 'avatar' => '/PUMA-Website/angel.JPG', 'status' => 'active'],

            // SPT Members
            ['name' => 'Ernest Teo', 'position' => 'Head of Student Passions and Talents', 'batch' => '2023', 'division' => 'SPT', 'avatar' => '/PUMA-Website/ernest.JPG', 'status' => 'inactive'],
            ['name' => 'Rivan Meinaki', 'position' => 'Vice of Student Passions and Talents', 'batch' => '2023', 'division' => 'SPT', 'avatar' => '/PUMA-Website/rivan.JPG', 'status' => 'inactive'],
            ['name' => 'Wisnu Alfian Nur Ashar', 'position' => 'Member of Student Passions and Talents', 'batch' => '2024', 'division' => 'SPT', 'avatar' => '/PUMA-Website/wisnu.JPG', 'status' => 'active'],
            ['name' => 'Fauzan Fajri', 'position' => 'Member of Student Passions and Talents', 'batch' => '2024', 'division' => 'SPT', 'avatar' => '/PUMA-Website/fauzan.JPG', 'status' => 'active'],
            ['name' => 'Qwyn Celine Djimondo', 'position' => 'Member of Student Passions and Talents', 'batch' => '2024', 'division' => 'SPT', 'avatar' => '/PUMA-Website/qwin.JPG', 'status' => 'active'],
        ];

        foreach ($members as $memberData) {
            $division = $divisions[$memberData['division']];

            if (!$division || !$kaustav) {
                echo "Skipping {$memberData['name']} - division or cabinet not found\n";
                continue;
            }

            $email = strtolower(str_replace(' ', '.', $memberData['name'])) . '@puma.com';

            // Check if user already exists
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Create user first
                $user = User::create([
                    'name' => $memberData['name'],
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'batch' => $memberData['batch'],
                    'avatar' => $memberData['avatar'],
                    'role' => 'member',
                    'status' => $memberData['status'],
                ]);
                echo "Created user: {$memberData['name']}\n";
            } else {
                echo "User already exists: {$memberData['name']}\n";
            }

            // Check if member record already exists
            $existingMember = Member::where('user_id', $user->id)
                ->where('cabinet_id', $kaustav->id)
                ->where('division_id', $division->id)
                ->first();

            if (!$existingMember) {
                // Create member record
                Member::create([
                    'user_id' => $user->id,
                    'cabinet_id' => $kaustav->id,
                    'division_id' => $division->id,
                    'position' => $memberData['position'],
                    'batch' => $memberData['batch'],
                    'status' => $memberData['status'],
                    'joined_date' => now()->subYears(intval(date('Y')) - intval($memberData['batch'])),
                ]);
                echo "Created member record for: {$memberData['name']}\n";
            } else {
                echo "Member record already exists for: {$memberData['name']}\n";
            }
        }
    }
}
