<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\Track;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Generate a certificate for a user.
     */
    public function generateCertificate(User $user, string $referenceType, int $referenceId): Certificate
    {
        $reference = $referenceType === 'course' 
            ? Course::find($referenceId)
            : Track::find($referenceId);

        if (!$reference) {
            throw new \Exception("Reference {$referenceType} with ID {$referenceId} not found");
        }

        // Check if certificate already exists
        $existingCertificate = $user->certificates()
            ->where('kind', $referenceType)
            ->where('ref_id', $referenceId)
            ->first();

        if ($existingCertificate) {
            return $existingCertificate;
        }

        // Get or create default template
        $template = $this->getDefaultTemplate($referenceType, $reference);

        // Generate certificate
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'kind' => $referenceType,
            'ref_id' => $referenceId,
            'title' => $this->generateCertificateTitle($reference, $referenceType),
            'description' => "Attestato di partecipazione al corso: {$reference->title}",
            'issued_at' => now(),
            'hours_total' => $reference->duration_minutes ? round($reference->duration_minutes / 60, 1) : 1,
            'public_uid' => Certificate::generatePublicUid(),
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'user_email' => $user->email,
                'reference_title' => $reference->title,
                'template_id' => $template->id,
            ],
        ]);

        // Generate PDF
        $this->generatePdf($certificate, $template, $user, $reference);

        return $certificate;
    }

    /**
     * Generate PDF for a certificate.
     */
    public function generatePdf(Certificate $certificate, CertificateTemplate $template, User $user, $reference): string
    {
        $html = $this->renderCertificateHtml($template, $user, $reference, $certificate);
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = "certificates/{$certificate->public_uid}.pdf";
        $pdfPath = storage_path("app/public/{$filename}");
        
        // Ensure directory exists
        $directory = dirname($pdfPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $pdf->save($pdfPath);
        
        // Update certificate with PDF path
        $certificate->update([
            'metadata' => array_merge($certificate->metadata ?? [], [
                'pdf_path' => $filename,
                'pdf_generated_at' => now()->toISOString(),
            ]),
        ]);
        
        return $filename;
    }

    /**
     * Render certificate HTML.
     */
    private function renderCertificateHtml(CertificateTemplate $template, User $user, $reference, Certificate $certificate): string
    {
        // Se il template ha un html_template personalizzato, usalo
        if ($template->html_template) {
            $html = $template->html_template;
        } else {
            // Altrimenti genera l'HTML basandosi sull'immagine di sfondo e i placeholder
            $html = $this->generateHtmlFromTemplate($template, $user, $reference, $certificate);
        }
        
        // Replace placeholders
        $replacements = [
            '{{user_name}}' => $user->first_name . ' ' . $user->last_name,
            '{{user_first_name}}' => $user->first_name,
            '{{user_last_name}}' => $user->last_name,
            '{{user_email}}' => $user->email,
            '{{reference_title}}' => $reference->title,
            '{{course_title}}' => $reference->title,
            '{{course_description}}' => $reference->description ?? '',
            '{{certificate_title}}' => $certificate->title,
            '{{certificate_date}}' => $certificate->issued_at->format('d/m/Y'),
            '{{issued_at}}' => $certificate->issued_at->format('d/m/Y'),
            '{{public_uid}}' => $certificate->public_uid,
            '{{certificate_public_uid}}' => $certificate->public_uid,
            '{{hours_total}}' => $certificate->hours_total,
            '{{current_date}}' => now()->format('d/m/Y'),
            '{{current_year}}' => now()->year,
            '{{city}}' => 'Roma',
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Generate HTML from template with background image and positioned placeholders.
     */
    private function generateHtmlFromTemplate(CertificateTemplate $template, User $user, $reference, Certificate $certificate): string
    {
        $backgroundImage = $template->background_image;
        $placeholderPositions = $template->settings['placeholder_positions'] ?? [];
        $styling = $template->settings['styling'] ?? [];
        
        // Se l'immagine è base64, usala direttamente
        if (str_starts_with($backgroundImage, 'data:image')) {
            $backgroundUrl = $backgroundImage;
        } else {
            // Altrimenti usa il path del file
            $backgroundUrl = asset('storage/' . $backgroundImage);
        }
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificato</title>
    <style>
        body { margin: 0; padding: 0; font-family: ' . ($styling['font_family'] ?? 'Inter') . '; }
        .certificate-container { 
            position: relative; 
            width: 842px; 
            height: 595px; 
            background-image: url("' . $backgroundUrl . '"); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            overflow: hidden;
        }';
        
        // Aggiungi stili per ogni placeholder posizionato
        foreach ($placeholderPositions as $index => $placeholder) {
            $fontSize = $placeholder['fontSize'];
            $color = $placeholder['color'];
            $fontFamily = $styling['font_family'] ?? 'Inter';
            
            // Personalizza stili per tipo di placeholder
            if ($placeholder['type'] === 'user_name') {
                $fontSize = $fontSize * 2; // Doppia grandezza
                $color = '#0B3B5E'; // Blu scuro
                $fontFamily = 'Brush Script MT, cursive'; // Carattere script
            } elseif ($placeholder['type'] === 'course_title') {
                $fontSize = $fontSize * 2; // Doppia grandezza
                $color = '#000000'; // Nero
            }
            
            $html .= '
        .placeholder-' . $index . ' {
            position: absolute;
            left: ' . $placeholder['x'] . 'px;
            top: ' . $placeholder['y'] . 'px;
            font-size: ' . $fontSize . 'px;
            color: ' . $color . ';
            font-weight: ' . $placeholder['fontWeight'] . ';
            font-family: ' . $fontFamily . ';
            white-space: nowrap;
            z-index: 10;
        }';
        }
        
        $html .= '
    </style>
</head>
<body>
    <div class="certificate-container">';
        
        // Aggiungi ogni placeholder posizionato
        foreach ($placeholderPositions as $index => $placeholder) {
            $text = $this->getPlaceholderText($placeholder['type'], $user, $reference, $certificate);
            $html .= '
        <div class="placeholder-' . $index . '">' . htmlspecialchars($text) . '</div>';
        }
        
        $html .= '
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Get text for placeholder type.
     */
    private function getPlaceholderText(string $type, User $user, $reference, Certificate $certificate): string
    {
        switch ($type) {
            case 'user_name':
                return $user->first_name . ' ' . $user->last_name;
            case 'course_title':
                return $reference->title;
            case 'certificate_date':
                return $certificate->issued_at->format('d/m/Y');
            case 'hours_total':
                return $certificate->hours_total . ' ore';
            case 'certificate_uid':
                return 'ID: ' . $certificate->public_uid;
            default:
                return '';
        }
    }

    /**
     * Get template for reference type and specific reference.
     */
    private function getDefaultTemplate(string $referenceType, $reference = null): CertificateTemplate
    {
        // Se abbiamo un riferimento specifico (corso), cerca il template associato
        if ($reference && $referenceType === 'course') {
            $template = CertificateTemplate::where('is_active', true)
                ->where('course_id', $reference->id)
                ->first();
            
            if ($template) {
                return $template;
            }
        }

        // Fallback: cerca il primo template attivo
        $template = CertificateTemplate::where('is_active', true)->first();

        if (!$template) {
            // Create default template
            $template = $this->createDefaultTemplate($referenceType);
        }

        return $template;
    }

    /**
     * Generate certificate title.
     */
    private function generateCertificateTitle($reference, string $referenceType): string
    {
        if ($referenceType === 'course') {
            return "Attestato di Partecipazione - {$reference->title}";
        } elseif ($referenceType === 'track') {
            return "Certificazione - {$reference->title}";
        }
        
        return "Attestato di Partecipazione";
    }

    /**
     * Create default certificate template.
     */
    private function createDefaultTemplate(string $referenceType): CertificateTemplate
    {
        $htmlTemplate = $this->getDefaultHtmlTemplate($referenceType);
        
        return CertificateTemplate::create([
            'name' => "Template Default {$referenceType}",
            'description' => "Template predefinito per certificati di tipo {$referenceType}",
            'template_type' => $referenceType,
            'html_template' => $htmlTemplate,
            'settings' => [
                'font_family' => 'Inter, sans-serif',
                'primary_color' => '#0B3B5E',
                'secondary_color' => '#00A7B7',
                'accent_color' => '#FFC857',
                'text_color' => '#14161A',
                'logo_position' => 'top-center',
                'signature_position' => 'bottom-right',
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Get default HTML template.
     */
    private function getDefaultHtmlTemplate(string $referenceType): string
    {
        $title = $referenceType === 'course' ? 'Certificato di Completamento Corso' : 'Certificato di Completamento Track';
        
        return '
        <!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{{certificate_title}}</title>
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");
                
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
                
                .certificate::after {
                    content: "✓";
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    width: 60px;
                    height: 60px;
                    background: #00A7B7;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 24px;
                    font-weight: bold;
                    opacity: 0.1;
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
            </style>
        </head>
        <body>
            <div class="certificate">
                <div class="header">
                    <div class="logo">CDF</div>
                    <h1 class="title">' . $title . '</h1>
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
     * Generate public UID for certificate.
     */
    private function generatePublicUid(): string
    {
        do {
            $uid = 'CDF-' . strtoupper(Str::random(8));
        } while (Certificate::where('public_uid', $uid)->exists());
        
        return $uid;
    }

    /**
     * Get certificate PDF content.
     */
    public function getCertificatePdf(Certificate $certificate): ?string
    {
        $pdfPath = $certificate->metadata['pdf_path'] ?? null;
        
        if (!$pdfPath) {
            return null;
        }
        
        $fullPath = storage_path("app/public/{$pdfPath}");
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        return file_get_contents($fullPath);
    }

    /**
     * Delete certificate PDF.
     */
    public function deleteCertificatePdf(Certificate $certificate): bool
    {
        $pdfPath = $certificate->metadata['pdf_path'] ?? null;
        
        if (!$pdfPath) {
            return true;
        }
        
        $fullPath = storage_path("app/public/{$pdfPath}");
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return true;
    }
}
