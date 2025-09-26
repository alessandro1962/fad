<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\Organization;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $startDate;
    protected $organizationId;

    public function __construct($startDate, $organizationId = null)
    {
        $this->startDate = $startDate;
        $this->organizationId = $organizationId;
    }

    public function collection()
    {
        $query = User::with(['organization', 'enrollments.course', 'progress.lesson'])
            ->where('created_at', '>=', $this->startDate)
            ->when($this->organizationId, function ($q) {
                return $q->where('organization_id', $this->organizationId);
            });

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID Utente',
            'Nome',
            'Email',
            'Organizzazione',
            'Data Registrazione',
            'Ultimo Accesso',
            'Corsi Iscritti',
            'Corsi Completati',
            'Ore di Apprendimento',
            'Lezioni Completate',
            'Badge Ottenuti',
            'Livello',
            'Punti Totali',
        ];
    }

    public function map($user): array
    {
        $totalLearningHours = $user->progress->sum('seconds_watched') / 3600;
        $completedLessons = $user->progress->where('completed', true)->count();
        $badgesCount = $user->badges->count();
        
        // Calculate user level based on points
        $totalPoints = $this->calculateUserPoints($user);
        $level = $this->calculateUserLevel($totalPoints);
        
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->organization?->name ?? 'N/A',
            $user->created_at->format('d/m/Y H:i'),
            $user->last_login_at?->format('d/m/Y H:i') ?? 'Mai',
            $user->enrollments->count(),
            $user->enrollments->where('status', 'completed')->count(),
            round($totalLearningHours, 2),
            $completedLessons,
            $badgesCount,
            $level,
            $totalPoints,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Analytics Utenti';
    }

    private function calculateUserPoints($user): int
    {
        $points = 0;
        
        // Points for completed courses (50 points each)
        $points += $user->enrollments->where('status', 'completed')->count() * 50;
        
        // Points for completed lessons (10 points each)
        $points += $user->progress->where('completed', true)->count() * 10;
        
        // Points for learning hours (1 point per hour)
        $points += round($user->progress->sum('seconds_watched') / 3600);
        
        // Points for badges (varies by badge)
        $points += $user->badges->sum('points') ?? 0;
        
        return $points;
    }

    private function calculateUserLevel($totalPoints): int
    {
        $pointsPerLevel = 100;
        $level = 1;
        
        while ($totalPoints >= $level * $pointsPerLevel && $level < 5) {
            $level++;
        }
        
        return $level;
    }
}
