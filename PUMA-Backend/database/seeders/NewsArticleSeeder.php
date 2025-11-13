<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'PUMA IT Launches New Website',
                'description' => 'We are excited to announce the launch of our brand new website with improved features and user experience.',
                'content' => "We are thrilled to announce the official launch of the PUMA IT organization's brand new website! This platform has been designed with our community in mind, offering enhanced features and a seamless user experience.\n\nThe new website includes comprehensive information about our events, news updates, member profiles, and much more. Our team has worked tirelessly to create a platform that truly represents the spirit of PUMA IT.\n\nWe invite all members and visitors to explore the new features and share their feedback with us. Stay tuned for more exciting updates and announcements!",
                'category' => 'announcement',
                'author' => 'PUMA IT Team',
                'featured_image' => 'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                'is_featured' => true,
                'published_at' => '2024-09-15 10:00:00',
            ],
            [
                'title' => 'Successful Regenetics Event 2024/2025',
                'description' => 'The PUMA IT Regenetics recruitment event concluded successfully with overwhelming participation.',
                'content' => "The PUMA Informatics Regeneration event for the academic year 2024/2025 has concluded with great success! We are delighted to welcome a new generation of enthusiastic members to our organization.\n\nThe event saw participation from over 100 aspiring members, all eager to be part of the PUMA IT family. Through various activities and presentations, candidates demonstrated their passion for technology and commitment to organizational excellence.\n\nWe look forward to working with our new members and guiding them through their journey in PUMA IT. Together, we will continue to innovate and excel in the field of information technology.",
                'category' => 'update',
                'author' => 'Recruitment Team',
                'featured_image' => 'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                'is_featured' => true,
                'published_at' => '2024-09-05 14:30:00',
            ],
            [
                'title' => 'Informatics Connect: Building Industry Partnerships',
                'description' => 'PUMA IT successfully organized Informatics Connect, bringing together students and industry professionals.',
                'content' => "PUMA IT's Informatics Connect event has successfully bridged the gap between academic learning and industry requirements. The event featured leading professionals from top tech companies sharing their insights and experiences.\n\nStudents had the opportunity to network with potential employers, learn about industry trends, and understand the skills required for successful careers in information technology.\n\nThe event included panel discussions, networking sessions, and one-on-one mentoring opportunities. We thank all our industry partners for their continued support and participation.",
                'category' => 'update',
                'author' => 'Event Coordination',
                'featured_image' => 'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                'is_featured' => false,
                'published_at' => '2025-03-10 16:00:00',
            ],
            [
                'title' => 'Member Achievement: National Coding Competition',
                'description' => 'PUMA IT members excel at the National Coding Competition, securing top positions.',
                'content' => "We are proud to announce that our members have achieved remarkable success at the National Coding Competition held last month. Our team secured first and third positions in the competitive programming category.\n\nThis achievement is a testament to the dedication and technical prowess of our members. Through continuous learning and practice sessions organized by PUMA IT, our members have honed their programming skills to compete at the national level.\n\nCongratulations to all participants and winners! Your success inspires others to strive for excellence.",
                'category' => 'announcement',
                'author' => 'Achievement Team',
                'featured_image' => 'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                'is_featured' => false,
                'published_at' => '2025-04-20 11:00:00',
            ],
            [
                'title' => 'Upcoming: Elevate Informatics Festival',
                'description' => 'Save the date for our annual Elevate Informatics Festival showcasing student projects and innovations.',
                'content' => "Mark your calendars! PUMA IT is excited to announce the upcoming Elevate Informatics Festival, scheduled for July 2025. This annual celebration will showcase the best student projects and achievements in information technology.\n\nThe festival will feature project exhibitions, technical workshops, guest speakers from the industry, and exciting competitions. It's an excellent opportunity for members to demonstrate their skills and innovations.\n\nStay tuned for registration details and event schedule. We look forward to celebrating innovation and excellence with the entire PUMA IT community!",
                'category' => 'announcement',
                'author' => 'Festival Committee',
                'featured_image' => 'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                'is_featured' => true,
                'published_at' => '2025-06-01 09:00:00',
            ],
        ];

        foreach ($articles as $articleData) {
            NewsArticle::create($articleData);
        }
    }
}
