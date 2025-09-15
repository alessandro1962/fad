<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // Milestone badges
            [
                'code' => 'first_login',
                'name' => 'Primo Accesso',
                'description' => 'Benvenuto su Campus Digitale Forma!',
                'icon' => '🎉',
                'color' => '#00A7B7',
                'category' => 'milestone',
                'rarity' => 1,
                'points' => 10,
                'criteria' => ['first_login' => true],
            ],
            [
                'code' => 'profile_complete',
                'name' => 'Profilo Completo',
                'description' => 'Hai completato il tuo profilo al 100%',
                'icon' => '👤',
                'color' => '#FFC857',
                'category' => 'milestone',
                'rarity' => 1,
                'points' => 15,
                'criteria' => ['profile_complete' => true],
            ],

            // Learning badges
            [
                'code' => 'first_lesson',
                'name' => 'Primo Passo',
                'description' => 'Hai completato la tua prima lezione',
                'icon' => '🎯',
                'color' => '#00A7B7',
                'category' => 'achievement',
                'rarity' => 1,
                'points' => 20,
                'criteria' => ['lessons_completed' => 1],
            ],
            [
                'code' => 'scholar',
                'name' => 'Studioso',
                'description' => 'Hai completato 5 lezioni',
                'icon' => '📚',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 1,
                'points' => 50,
                'criteria' => ['lessons_completed' => 5],
            ],
            [
                'code' => 'apprentice',
                'name' => 'Apprendista',
                'description' => 'Hai completato 10 lezioni',
                'icon' => '🎓',
                'color' => '#FFC857',
                'category' => 'achievement',
                'rarity' => 2,
                'points' => 100,
                'criteria' => ['lessons_completed' => 10],
            ],
            [
                'code' => 'expert',
                'name' => 'Esperto',
                'description' => 'Hai completato 25 lezioni',
                'icon' => '🏆',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 3,
                'points' => 250,
                'criteria' => ['lessons_completed' => 25],
            ],
            [
                'code' => 'master',
                'name' => 'Maestro',
                'description' => 'Hai completato 50 lezioni',
                'icon' => '👑',
                'color' => '#FFC857',
                'category' => 'achievement',
                'rarity' => 4,
                'points' => 500,
                'criteria' => ['lessons_completed' => 50],
            ],

            // Course completion badges
            [
                'code' => 'first_course',
                'name' => 'Primo Corso',
                'description' => 'Hai completato il tuo primo corso',
                'icon' => '🎉',
                'color' => '#00A7B7',
                'category' => 'achievement',
                'rarity' => 1,
                'points' => 100,
                'criteria' => ['courses_completed' => 1],
            ],
            [
                'code' => 'dedicated_student',
                'name' => 'Studente Dedicato',
                'description' => 'Hai completato 3 corsi',
                'icon' => '📖',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 2,
                'points' => 300,
                'criteria' => ['courses_completed' => 3],
            ],
            [
                'code' => 'advanced_student',
                'name' => 'Studente Avanzato',
                'description' => 'Hai completato 5 corsi',
                'icon' => '🎓',
                'color' => '#FFC857',
                'category' => 'achievement',
                'rarity' => 3,
                'points' => 500,
                'criteria' => ['courses_completed' => 5],
            ],
            [
                'code' => 'expert_student',
                'name' => 'Studente Esperto',
                'description' => 'Hai completato 10 corsi',
                'icon' => '🏆',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 4,
                'points' => 1000,
                'criteria' => ['courses_completed' => 10],
            ],

            // Learning hours badges
            [
                'code' => 'study_hour',
                'name' => 'Ora di Studio',
                'description' => 'Hai completato 1 ora di apprendimento',
                'icon' => '⏰',
                'color' => '#00A7B7',
                'category' => 'achievement',
                'rarity' => 1,
                'points' => 25,
                'criteria' => ['hours_watched' => 1],
            ],
            [
                'code' => 'marathoner',
                'name' => 'Maratoneta',
                'description' => 'Hai completato 10 ore di apprendimento',
                'icon' => '🏃',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 2,
                'points' => 250,
                'criteria' => ['hours_watched' => 10],
            ],
            [
                'code' => 'ultra_marathoner',
                'name' => 'Ultra Maratoneta',
                'description' => 'Hai completato 25 ore di apprendimento',
                'icon' => '🚀',
                'color' => '#FFC857',
                'category' => 'achievement',
                'rarity' => 3,
                'points' => 500,
                'criteria' => ['hours_watched' => 25],
            ],
            [
                'code' => 'legend',
                'name' => 'Leggenda',
                'description' => 'Hai completato 50 ore di apprendimento',
                'icon' => '🌟',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 4,
                'points' => 1000,
                'criteria' => ['hours_watched' => 50],
            ],

            // Quiz badges
            [
                'code' => 'first_quiz',
                'name' => 'Primo Quiz',
                'description' => 'Hai superato il tuo primo quiz',
                'icon' => '✅',
                'color' => '#00A7B7',
                'category' => 'achievement',
                'rarity' => 1,
                'points' => 30,
                'criteria' => ['quizzes_passed' => 1],
            ],
            [
                'code' => 'quiz_master',
                'name' => 'Quiz Master',
                'description' => 'Hai superato 5 quiz',
                'icon' => '🧠',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 2,
                'points' => 150,
                'criteria' => ['quizzes_passed' => 5],
            ],
            [
                'code' => 'perfect',
                'name' => 'Perfetto',
                'description' => 'Hai ottenuto un punteggio perfetto in un quiz',
                'icon' => '💯',
                'color' => '#FFC857',
                'category' => 'achievement',
                'rarity' => 3,
                'points' => 100,
                'criteria' => ['perfect_scores' => 1],
            ],
            [
                'code' => 'perfectionist',
                'name' => 'Perfezionista',
                'description' => 'Hai ottenuto 3 punteggi perfetti',
                'icon' => '🎯',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 4,
                'points' => 300,
                'criteria' => ['perfect_scores' => 3],
            ],

            // Achievement badges
            [
                'code' => 'first_certificate',
                'name' => 'Primo Certificato',
                'description' => 'Hai ottenuto il tuo primo certificato',
                'icon' => '📜',
                'color' => '#00A7B7',
                'category' => 'achievement',
                'rarity' => 1,
                'points' => 200,
                'criteria' => ['certificates_earned' => 1],
            ],
            [
                'code' => 'collector',
                'name' => 'Collezionista',
                'description' => 'Hai ottenuto 3 certificati',
                'icon' => '🏅',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 2,
                'points' => 600,
                'criteria' => ['certificates_earned' => 3],
            ],
            [
                'code' => 'certified_expert',
                'name' => 'Esperto Certificato',
                'description' => 'Hai ottenuto 5 certificati',
                'icon' => '🎖️',
                'color' => '#0B3B5E',
                'category' => 'achievement',
                'rarity' => 3,
                'points' => 1000,
                'criteria' => ['certificates_earned' => 5],
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::create($badgeData);
        }
    }
}