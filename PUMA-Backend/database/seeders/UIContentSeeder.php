<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UIContent;

class UIContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            [
                'key' => 'homepage_hero_title',
                'title' => 'Homepage Hero Title',
                'content' => 'Welcome to PUMA',
                'type' => 'text',
                'is_active' => true,
                'display_order' => 1,
                'metadata' => json_encode(['section' => 'hero']),
            ],
            [
                'key' => 'homepage_hero_subtitle',
                'title' => 'Homepage Hero Subtitle',
                'content' => 'Empowering Future Leaders Through Technology',
                'type' => 'text',
                'is_active' => true,
                'display_order' => 2,
                'metadata' => json_encode(['section' => 'hero']),
            ],
            [
                'key' => 'about_section_title',
                'title' => 'About Section Title',
                'content' => 'About PUMA',
                'type' => 'text',
                'is_active' => true,
                'display_order' => 3,
                'metadata' => json_encode(['section' => 'about']),
            ],
            [
                'key' => 'about_section_content',
                'title' => 'About Section Content',
                'content' => 'PUMA is a student organization dedicated to fostering innovation and excellence in technology.',
                'type' => 'html',
                'is_active' => true,
                'display_order' => 4,
                'metadata' => json_encode(['section' => 'about']),
            ],
            [
                'key' => 'contact_email',
                'title' => 'Contact Email',
                'content' => 'contact@puma.org',
                'type' => 'text',
                'is_active' => true,
                'display_order' => 5,
                'metadata' => json_encode(['section' => 'contact']),
            ],
        ];

        foreach ($contents as $content) {
            UIContent::updateOrCreate(
                ['key' => $content['key']],
                $content
            );
        }
    }
}
