<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CertificateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template predefinito per corsi
        CertificateTemplate::create([
            'name' => 'Template Predefinito Corsi',
            'description' => 'Template standard per attestati di corsi completati',
            'type' => 'course',
            'background_type' => 'html',
            'background_data' => $this->getDefaultCourseTemplate(),
            'placeholder_positions' => [
                'user_name' => ['x' => 50, 'y' => 40, 'align' => 'center'],
                'course_title' => ['x' => 50, 'y' => 50, 'align' => 'center'],
                'certificate_date' => ['x' => 20, 'y' => 80, 'align' => 'left'],
                'signature' => ['x' => 80, 'y' => 80, 'align' => 'right'],
                'certificate_id' => ['x' => 50, 'y' => 90, 'align' => 'center'],
            ],
            'styling' => [
                'font_family' => 'Inter',
                'primary_color' => '#0B3B5E',
                'secondary_color' => '#00A7B7',
                'accent_color' => '#FFC857',
                'background_color' => '#F4F1EA',
            ],
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Template per percorsi/track
        CertificateTemplate::create([
            'name' => 'Template Percorsi',
            'description' => 'Template per attestati di percorsi completati',
            'type' => 'track',
            'background_type' => 'html',
            'background_data' => $this->getDefaultTrackTemplate(),
            'placeholder_positions' => [
                'user_name' => ['x' => 50, 'y' => 40, 'align' => 'center'],
                'track_title' => ['x' => 50, 'y' => 50, 'align' => 'center'],
                'certificate_date' => ['x' => 20, 'y' => 80, 'align' => 'left'],
                'signature' => ['x' => 80, 'y' => 80, 'align' => 'right'],
                'certificate_id' => ['x' => 50, 'y' => 90, 'align' => 'center'],
            ],
            'styling' => [
                'font_family' => 'Inter',
                'primary_color' => '#0B3B5E',
                'secondary_color' => '#00A7B7',
                'accent_color' => '#FFC857',
                'background_color' => '#F4F1EA',
            ],
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Template personalizzabile
        CertificateTemplate::create([
            'name' => 'Template Personalizzabile',
            'description' => 'Template base per personalizzazioni avanzate',
            'type' => 'custom',
            'background_type' => 'html',
            'background_data' => $this->getCustomTemplate(),
            'placeholder_positions' => [
                'user_name' => ['x' => 50, 'y' => 40, 'align' => 'center'],
                'title' => ['x' => 50, 'y' => 50, 'align' => 'center'],
                'certificate_date' => ['x' => 20, 'y' => 80, 'align' => 'left'],
                'signature' => ['x' => 80, 'y' => 80, 'align' => 'right'],
                'certificate_id' => ['x' => 50, 'y' => 90, 'align' => 'center'],
            ],
            'styling' => [
                'font_family' => 'Inter',
                'primary_color' => '#0B3B5E',
                'secondary_color' => '#00A7B7',
                'accent_color' => '#FFC857',
                'background_color' => '#F4F1EA',
            ],
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }

    /**
     * Get default course template HTML.
     */
    private function getDefaultCourseTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #F4F1EA 0%, #E8E3D3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .certificate {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(11, 59, 94, 0.1);
            padding: 60px;
            max-width: 800px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .certificate::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #0B3B5E 0%, #00A7B7 50%, #FFC857 100%);
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0B3B5E, #00A7B7);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 700;
        }
        
        .title {
            font-size: 32px;
            font-weight: 700;
            color: #0B3B5E;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 18px;
            color: #00A7B7;
            font-weight: 500;
        }
        
        .content {
            text-align: center;
            margin: 40px 0;
        }
        
        .award-text {
            font-size: 20px;
            color: #14161A;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .recipient {
            font-size: 28px;
            font-weight: 600;
            color: #0B3B5E;
            margin-bottom: 20px;
        }
        
        .course-title {
            font-size: 24px;
            font-weight: 500;
            color: #00A7B7;
            margin-bottom: 30px;
            padding: 20px;
            background: #F4F1EA;
            border-radius: 12px;
            border-left: 4px solid #FFC857;
        }
        
        .achievement-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #FFC857, #FFB347);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(255, 200, 87, 0.3);
        }
        
        .badge-icon {
            font-size: 32px;
            margin-right: 15px;
        }
        
        .badge-text {
            font-size: 18px;
            font-weight: 600;
            color: #0B3B5E;
        }
        
        .date-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #E8E3D3;
        }
        
        .date {
            font-size: 16px;
            color: #14161A;
        }
        
        .signature {
            text-align: right;
        }
        
        .signature-line {
            width: 200px;
            height: 2px;
            background: #0B3B5E;
            margin: 10px 0 5px auto;
        }
        
        .signature-text {
            font-size: 14px;
            color: #14161A;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E8E3D3;
            font-size: 12px;
            color: #666;
        }
        
        .certificate-id {
            font-family: monospace;
            background: #F4F1EA;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="logo">CDF</div>
            <h1 class="title">Attestato di Partecipazione</h1>
            <p class="subtitle">Campus Digitale Forma</p>
        </div>
        
        <div class="content">
            <p class="award-text">
                Si attesta che
            </p>
            
            <div class="recipient">{{user_name}}</div>
            
            <p class="award-text">
                ha completato con successo il corso
            </p>
            
            <div class="course-title">{{course_title}}</div>
            
            <p class="award-text">
                con una durata di <strong>{{hours_total}} ore</strong>, dimostrando competenza e dedizione nell\'apprendimento.
            </p>
            
            <div class="achievement-badge">
                <div class="badge-icon">🏆</div>
                <div class="badge-text">Completamento Riconosciuto</div>
            </div>
        </div>
        
        <div class="date-section">
            <div class="date">
                Data: {{certificate_date}}
            </div>
            
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-text">Campus Digitale Forma</div>
            </div>
        </div>
        
        <div class="footer">
            <p>Questo certificato è stato generato digitalmente e può essere verificato online.</p>
            <p>Luogo di rilascio: {{city}} | Data di completamento: {{issued_at}}</p>
            <div class="certificate-id">ID Certificato: {{certificate_public_uid}}</div>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Get default track template HTML.
     */
    private function getDefaultTrackTemplate(): string
    {
        return str_replace('corso', 'percorso', $this->getDefaultCourseTemplate());
    }

    /**
     * Get custom template HTML.
     */
    private function getCustomTemplate(): string
    {
        return $this->getDefaultCourseTemplate();
    }
}
