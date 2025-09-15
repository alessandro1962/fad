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
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->first();

        if ($existingCertificate) {
            return $existingCertificate;
        }

        // Get or create default template
        $template = $this->getDefaultTemplate($referenceType);

        // Generate certificate
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'title' => $this->generateCertificateTitle($reference, $referenceType),
            'public_uid' => $this->generatePublicUid(),
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'user_name' => $user->name,
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
        $html = $template->html_template;
        
        // Replace placeholders
        $replacements = [
            '{{user_name}}' => $user->name,
            '{{user_first_name}}' => $user->first_name ?? explode(' ', $user->name)[0],
            '{{user_last_name}}' => $user->last_name ?? (count(explode(' ', $user->name)) > 1 ? explode(' ', $user->name)[1] : ''),
            '{{user_email}}' => $user->email,
            '{{course_title}}' => $reference->title,
            '{{course_description}}' => $reference->description ?? '',
            '{{certificate_title}}' => $certificate->title,
            '{{certificate_date}}' => $certificate->created_at->format('d/m/Y'),
            '{{certificate_public_uid}}' => $certificate->public_uid,
            '{{current_date}}' => now()->format('d/m/Y'),
            '{{current_year}}' => now()->year,
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Get default template for reference type.
     */
    private function getDefaultTemplate(string $referenceType): CertificateTemplate
    {
        $template = CertificateTemplate::active()
            ->ofType($referenceType)
            ->first();

        if (!$template) {
            // Create default template
            $template = $this->createDefaultTemplate($referenceType);
        }

        return $template;
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
                        ha completato con successo il
                    </p>
                    
                    <div class="course-title">{{course_title}}</div>
                    
                    <p class="award-text">
                        dimostrando competenza e dedizione nell\'apprendimento.
                    </p>
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
                    <div class="certificate-id">ID: {{certificate_public_uid}}</div>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Generate certificate title.
     */
    private function generateCertificateTitle($reference, string $referenceType): string
    {
        $type = $referenceType === 'course' ? 'Corso' : 'Track';
        return "Certificato di Completamento - {$type}: {$reference->title}";
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
