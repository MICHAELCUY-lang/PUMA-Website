<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Regenetics 2024/2025',
                'event_date' => '2024-09-01',
                'description' => 'The PUMA Informatics Regeneration is an event to recruit new members who are enthusiastic and committed to joining the organization.',
                'status' => 'completed',
                'location' => 'Main Hall',
                'category' => 'Recruitment',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample1.jpg',
                    '../public/sample2.jpg'
                ],
            ],
            [
                'title' => 'Unitics',
                'event_date' => '2024-12-01',
                'description' => 'Unitics (Unity of Informatics) is organized to welcome new members to PUMA IT. The primary focus is to create an inclusive environment.',
                'status' => 'completed',
                'location' => 'Campus Ground',
                'category' => 'Welcome Event',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample3.jpg',
                    '../public/sample4.jpg'
                ],
            ],
            [
                'title' => '1st Aformation Midterm',
                'event_date' => '2024-10-01',
                'description' => 'First midterm evaluation for new members focusing on organizational knowledge and technical skills development.',
                'status' => 'completed',
                'location' => 'Training Room',
                'category' => 'Evaluation',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample5.jpg',
                    '../public/sample6.jpg'
                ],
            ],
            [
                'title' => 'PUMA Training',
                'event_date' => '2024-10-01',
                'description' => 'Comprehensive training program for members covering technical skills and organizational knowledge.',
                'status' => 'completed',
                'location' => 'Computer Lab',
                'category' => 'Training',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample7.jpg',
                    '../public/sample8.jpg'
                ],
            ],
            [
                'title' => 'Brainstormics',
                'event_date' => '2024-11-01',
                'description' => 'Collaborative brainstorming session to generate innovative ideas for upcoming projects and events.',
                'status' => 'completed',
                'location' => 'Meeting Room',
                'category' => 'Workshop',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample10.jpg'
                ],
            ],
            [
                'title' => 'Guest Lecture',
                'event_date' => '2024-12-01',
                'description' => 'Industry professionals sharing insights and expertise on current trends in information technology.',
                'status' => 'completed',
                'location' => 'Auditorium',
                'category' => 'Seminar',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample12.jpg'
                ],
            ],
            [
                'title' => '2nd Aformation Final Exam',
                'event_date' => '2024-12-01',
                'description' => 'End-of-semester evaluation assessing progress and knowledge acquisition of new members.',
                'status' => 'completed',
                'location' => 'Exam Hall',
                'category' => 'Evaluation',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample14.jpg'
                ],
            ],
            [
                'title' => 'Temu Alumni',
                'event_date' => '2025-02-01',
                'description' => 'Networking event connecting current members with alumni to share experiences and career insights.',
                'status' => 'completed',
                'location' => 'Alumni Hall',
                'category' => 'Networking',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample16.jpg'
                ],
            ],
            [
                'title' => '3rd Aformation Midterm',
                'event_date' => '2025-02-01',
                'description' => 'Midterm evaluation focusing on project development and leadership skills assessment.',
                'status' => 'completed',
                'location' => 'Training Room',
                'category' => 'Evaluation',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample18.jpg'
                ],
            ],
            [
                'title' => 'Informatics Connect',
                'event_date' => '2025-03-01',
                'description' => 'Industry networking event connecting students with potential employers and industry partners.',
                'status' => 'completed',
                'location' => 'Convention Center',
                'category' => 'Networking',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample20.jpg'
                ],
            ],
            [
                'title' => '4th Aformation Final Exam',
                'event_date' => '2025-05-01',
                'description' => 'Final comprehensive evaluation of member progress throughout the academic year.',
                'status' => 'completed',
                'location' => 'Exam Hall',
                'category' => 'Evaluation',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample22.jpg'
                ],
            ],
            [
                'title' => 'Preschotics Beasiswa',
                'event_date' => '2025-05-01',
                'description' => 'Scholarship preparation program helping students apply for prestigious educational opportunities.',
                'status' => 'completed',
                'location' => 'Counseling Room',
                'category' => 'Workshop',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample22.jpg'
                ],
            ],
            [
                'title' => 'Company Visit',
                'event_date' => '2025-06-01',
                'description' => 'Organized visits to leading tech companies to observe professional work environments.',
                'status' => 'upcoming',
                'location' => 'Various Tech Companies',
                'category' => 'Field Trip',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample22.jpg'
                ],
            ],
            [
                'title' => 'Elevate Informatics Festival',
                'event_date' => '2025-07-01',
                'description' => 'Annual celebration showcasing student projects and achievements in information technology.',
                'status' => 'upcoming',
                'location' => 'Main Campus',
                'category' => 'Festival',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample22.jpg'
                ],
            ],
            [
                'title' => 'Regenetics',
                'event_date' => '2025-08-01',
                'description' => 'Recruitment event for the next academic year welcoming new potential organization members.',
                'status' => 'upcoming',
                'location' => 'Main Hall',
                'category' => 'Recruitment',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample22.jpg'
                ],
            ],
            [
                'title' => 'Inforuum',
                'event_date' => '2025-08-01',
                'description' => 'Open forum discussion addressing current challenges and future directions in informatics.',
                'status' => 'upcoming',
                'location' => 'Auditorium',
                'category' => 'Forum',
                'images' => [
                    'https://i.pinimg.com/736x/ff/e1/6c/ffe16ca3153ef85b42f5cfebbd69c758.jpg',
                    '../public/sample22.jpg'
                ],
            ],
        ];

        foreach ($events as $eventData) {
            $images = $eventData['images'];
            unset($eventData['images']);

            $event = Event::create($eventData);

            // Create event images
            foreach ($images as $imageUrl) {
                EventImage::create([
                    'event_id' => $event->id,
                    'image_url' => $imageUrl,
                ]);
            }
        }
    }
}
