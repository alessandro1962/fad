<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Track;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }
    /**
     * Get user's certificates.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $certificates = $user->certificates()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($certificate) {
                return [
                    'id' => $certificate->id,
                    'public_uid' => $certificate->public_uid,
                    'title' => $certificate->title,
                    'description' => $certificate->description,
                    'kind' => $certificate->kind,
                    'ref_id' => $certificate->ref_id,
                    'issued_at' => $certificate->issued_at->toISOString(),
                    'hours_total' => $certificate->hours_total,
                    'pdf_path' => $certificate->pdf_path,
                    'metadata' => $certificate->metadata,
                ];
            });

        return response()->json([
            'data' => $certificates,
        ]);
    }

    /**
     * Get a specific certificate.
     */
    public function show(Request $request, Certificate $certificate): JsonResponse
    {
        $user = $request->user();
        
        // Verify the certificate belongs to the user
        if ($certificate->user_id !== $user->id) {
            return response()->json([
                'message' => 'Certificato non trovato.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $certificate->id,
                'public_uid' => $certificate->public_uid,
                'title' => $certificate->title,
                'type' => $certificate->reference_type,
                'reference_id' => $certificate->reference_id,
                'reference_title' => $certificate->reference->title ?? 'N/A',
                'issued_at' => $certificate->created_at->toISOString(),
                'has_pdf' => $certificate->hasPdf(),
                'public_url' => $certificate->publicUrl,
                'metadata' => $certificate->metadata,
            ],
        ]);
    }

    /**
     * Generate a certificate for a completed course.
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'reference_type' => ['required', 'in:course,track'],
            'reference_id' => ['required', 'integer'],
        ]);

        $user = $request->user();
        
        // Check if certificate already exists
        $existingCertificate = $user->certificates()
            ->where('reference_type', $request->reference_type)
            ->where('reference_id', $request->reference_id)
            ->first();

        if ($existingCertificate) {
            return response()->json([
                'message' => 'Certificato già esistente.',
                'data' => [
                    'certificate_id' => $existingCertificate->id,
                    'public_uid' => $existingCertificate->public_uid,
                ],
            ], 409);
        }

        // Verify completion requirements
        if ($request->reference_type === 'course') {
            $course = Course::find($request->reference_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Corso non trovato.',
                ], 404);
            }

            $enrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->where('status', 'completed')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'message' => 'Devi completare il corso prima di poter generare il certificato.',
                ], 403);
            }
        } else {
            $track = Track::find($request->reference_id);
            if (!$track) {
                return response()->json([
                    'message' => 'Track non trovato.',
                ], 404);
            }

            $userTrack = $user->userTracks()
                ->where('track_id', $track->id)
                ->where('status', 'completed')
                ->first();

            if (!$userTrack) {
                return response()->json([
                    'message' => 'Devi completare il track prima di poter generare il certificato.',
                ], 403);
            }
        }

        // Generate certificate using service
        $certificate = $this->certificateService->generateCertificate(
            $user,
            $request->reference_type,
            $request->reference_id
        );

        return response()->json([
            'message' => 'Certificato generato con successo.',
            'data' => [
                'certificate_id' => $certificate->id,
                'public_uid' => $certificate->public_uid,
                'title' => $certificate->title,
                'public_url' => $certificate->publicUrl,
            ],
        ], 201);
    }

    /**
     * Get certificate by public UID (for sharing).
     */
    public function public(Request $request, string $publicUid): JsonResponse
    {
        $certificate = Certificate::where('public_uid', $publicUid)->first();

        if (!$certificate) {
            return response()->json([
                'message' => 'Certificato non trovato.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $certificate->id,
                'public_uid' => $certificate->public_uid,
                'title' => $certificate->title,
                'type' => $certificate->reference_type,
                'reference_title' => $certificate->reference->title ?? 'N/A',
                'issued_at' => $certificate->created_at->toISOString(),
                'has_pdf' => $certificate->hasPdf(),
                'metadata' => $certificate->metadata,
            ],
        ]);
    }

    /**
     * Download certificate PDF by public UID.
     */
    public function publicDownload(string $publicUid): Response
    {
        $certificate = Certificate::where('public_uid', $publicUid)->first();

        if (!$certificate) {
            return response()->json([
                'message' => 'Certificato non trovato.',
            ], 404);
        }

        $pdfContent = $this->certificateService->getCertificatePdf($certificate);

        if (!$pdfContent) {
            return response()->json([
                'message' => 'PDF del certificato non disponibile.',
            ], 404);
        }

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="certificato-' . $certificate->public_uid . '.pdf"');
    }

    /**
     * Download certificate PDF.
     */
    public function download(Request $request, Certificate $certificate): Response
    {
        $user = $request->user();
        
        // Verify the certificate belongs to the user
        if ($certificate->user_id !== $user->id) {
            return response()->json([
                'message' => 'Certificato non trovato.',
            ], 404);
        }

        $pdfContent = $this->certificateService->getCertificatePdf($certificate);

        if (!$pdfContent) {
            return response()->json([
                'message' => 'PDF del certificato non disponibile.',
            ], 404);
        }

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="certificato-' . $certificate->public_uid . '.pdf"');
    }

}