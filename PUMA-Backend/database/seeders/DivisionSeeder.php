<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            [
                'code' => 'BOD',
                'name' => 'BOD',
                'title' => 'Board of Directors',
                'description' => 'The Board of Directors leads the organization, sets strategic direction, and oversees all activities to ensure alignment with PUMA IT\'s mission and vision.',
                'image' => '/division/bod.JPG',
            ],
            [
                'code' => 'RNT',
                'name' => 'RNT',
                'title' => 'Research and Technology',
                'description' => 'Research and Technology division focuses on innovation, technological advancement, and research initiatives to keep PUMA IT at the forefront of information technology.',
                'image' => '/division/rnt.jpg',
            ],
            [
                'code' => 'HRD',
                'name' => 'HRD',
                'title' => 'Human Resources Development',
                'description' => 'Human Resources Development is responsible for member recruitment, training, development, and ensuring a positive organizational culture.',
                'image' => '/division/hrd.JPG',
            ],
            [
                'code' => 'ICM',
                'name' => 'ICM',
                'title' => 'Information and Creative Media',
                'description' => 'Information and Creative Media manages all communication channels, creates engaging content, and maintains PUMA IT\'s brand identity and public image.',
                'image' => '/division/icm.JPG',
            ],
            [
                'code' => 'IR',
                'name' => 'IR',
                'title' => 'Internal Relations',
                'description' => 'Internal Relations fosters strong relationships among members, coordinates internal events, and ensures effective communication within the organization.',
                'image' => '/division/internal.JPG',
            ],
            [
                'code' => 'ER',
                'name' => 'ER',
                'title' => 'External Relations',
                'description' => 'External Relations builds and maintains partnerships with external organizations, industries, and institutions to expand PUMA IT\'s network and opportunities.',
                'image' => '/division/EXT.JPG',
            ],
            [
                'code' => 'SAC',
                'name' => 'SAC',
                'title' => 'Student Academic & Competition',
                'description' => 'Student Academic & Competition supports members in academic excellence and competitive programming, organizing training and competition participation.',
                'image' => '/division/sac.JPG',
            ],
            [
                'code' => 'SPT',
                'name' => 'SPT',
                'title' => 'Student Passions & Talents',
                'description' => 'Student Passions & Talents encourages members to explore and develop their interests and talents beyond academics through various activities and programs.',
                'image' => '/division/spt.JPG',
            ],
            [
                'code' => 'TECHNOPRENEUR',
                'name' => 'TECHNOPRENEUR',
                'title' => 'Technopreneur',
                'description' => 'Technopreneur division cultivates entrepreneurial mindset among members, providing guidance and support for technology-based business initiatives and startups.',
                'image' => '/division/Technoprenet.JPG',
            ],
            [
                'code' => 'RNC',
                'name' => 'RNC',
                'title' => 'Relations and Communications',
                'description' => 'Relations and Communications manages both internal and external relationships, fostering communication and building strategic partnerships.',
                'image' => '/division/rnc.JPG',
            ],
            [
                'code' => 'BD',
                'name' => 'BD',
                'title' => 'Business Development',
                'description' => 'Business Development focuses on creating business opportunities, developing strategic partnerships, and driving organizational growth through innovative initiatives.',
                'image' => '/division/bd.JPG',
            ],
        ];

        foreach ($divisions as $divisionData) {
            Division::create($divisionData);
        }
    }
}
