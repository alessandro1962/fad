<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all modules
        $modules = Module::all();
        
        if ($modules->isEmpty()) {
            $this->command->info('Nessun modulo trovato. Creare prima i moduli.');
            return;
        }

        foreach ($modules as $module) {
            // Create video lessons for each module
            $lessons = [
                [
                    'title' => 'Introduzione al Modulo',
                    'description' => 'Lezione introduttiva del modulo ' . $module->title,
                    'type' => 'video',
                    'order' => 1,
                    'duration_minutes' => 15,
                    'payload' => [
                        'provider' => 'youtube',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'completion_threshold' => 0.8,
                        'block_progression' => true
                    ],
                    'is_active' => true
                ],
                [
                    'title' => 'Concetti Fondamentali',
                    'description' => 'I concetti fondamentali del modulo ' . $module->title,
                    'type' => 'video',
                    'order' => 2,
                    'duration_minutes' => 20,
                    'payload' => [
                        'provider' => 'vimeo',
                        'video_id' => '148751763',
                        'completion_threshold' => 0.9,
                        'block_progression' => true
                    ],
                    'is_active' => true
                ],
                [
                    'title' => 'Esercitazione Pratica',
                    'description' => 'Esercitazione pratica per il modulo ' . $module->title,
                    'type' => 'video',
                    'order' => 3,
                    'duration_minutes' => 25,
                    'payload' => [
                        'provider' => 'upload',
                        'video_url' => '/storage/videos/sample-video.mp4',
                        'completion_threshold' => 0.85,
                        'block_progression' => true
                    ],
                    'is_active' => true
                ]
            ];

            foreach ($lessons as $lessonData) {
                $lessonData['module_id'] = $module->id;
                Lesson::create($lessonData);
            }
        }

        $this->command->info('Lezioni create con successo per ' . $modules->count() . ' moduli.');
    }
}
