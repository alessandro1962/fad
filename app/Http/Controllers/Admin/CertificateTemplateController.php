<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $templates = CertificateTemplate::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $templates,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:course,track,custom',
            'background_type' => 'required|in:html,pdf,image',
            'background_data' => 'nullable|string',
            'background_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'placeholder_positions' => 'nullable|array',
            'styling' => 'nullable|array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('background_file')) {
            $file = $request->file('background_file');
            $filename = 'certificate-templates/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public', $filename);
            $validated['background_data'] = $filename;
        }

        // Set default values
        $validated['is_default'] = $validated['is_default'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = CertificateTemplate::max('sort_order') + 1;

        $template = CertificateTemplate::create($validated);

        return response()->json([
            'message' => 'Template creato con successo.',
            'data' => $template,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CertificateTemplate $certificateTemplate): JsonResponse
    {
        return response()->json([
            'data' => $certificateTemplate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CertificateTemplate $certificateTemplate): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:course,track,custom',
            'background_type' => 'sometimes|in:html,pdf,image',
            'background_data' => 'nullable|string',
            'background_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'placeholder_positions' => 'nullable|array',
            'styling' => 'nullable|array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('background_file')) {
            // Delete old file if exists
            if ($certificateTemplate->background_data && $certificateTemplate->background_type !== 'html') {
                Storage::delete('public/' . $certificateTemplate->background_data);
            }

            $file = $request->file('background_file');
            $filename = 'certificate-templates/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public', $filename);
            $validated['background_data'] = $filename;
        }

        $certificateTemplate->update($validated);

        return response()->json([
            'message' => 'Template aggiornato con successo.',
            'data' => $certificateTemplate,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CertificateTemplate $certificateTemplate): JsonResponse
    {
        // Don't allow deletion of default templates
        if ($certificateTemplate->is_default) {
            return response()->json([
                'message' => 'Non è possibile eliminare un template predefinito.',
            ], 403);
        }

        // Delete associated file
        if ($certificateTemplate->background_data && $certificateTemplate->background_type !== 'html') {
            Storage::delete('public/' . $certificateTemplate->background_data);
        }

        $certificateTemplate->delete();

        return response()->json([
            'message' => 'Template eliminato con successo.',
        ]);
    }

    /**
     * Preview template with sample data.
     */
    public function preview(CertificateTemplate $certificateTemplate): JsonResponse
    {
        $sampleData = [
            'user_name' => 'Mario Rossi',
            'course_title' => 'Corso di Esempio',
            'certificate_date' => now()->format('d/m/Y'),
            'certificate_id' => 'CERT-SAMPLE123',
            'hours_total' => 5,
            'city' => 'Roma',
        ];

        return response()->json([
            'data' => [
                'template' => $certificateTemplate,
                'sample_data' => $sampleData,
                'preview_html' => $this->generatePreviewHtml($certificateTemplate, $sampleData),
            ],
        ]);
    }

    /**
     * Set template as default.
     */
    public function setDefault(CertificateTemplate $certificateTemplate): JsonResponse
    {
        // Remove default from other templates of same type
        CertificateTemplate::where('type', $certificateTemplate->type)
            ->where('id', '!=', $certificateTemplate->id)
            ->update(['is_default' => false]);

        $certificateTemplate->update(['is_default' => true]);

        return response()->json([
            'message' => 'Template impostato come predefinito.',
            'data' => $certificateTemplate,
        ]);
    }

    /**
     * Generate preview HTML for template.
     */
    private function generatePreviewHtml(CertificateTemplate $template, array $data): string
    {
        if ($template->background_type === 'html') {
            $html = $template->background_data;
        } else {
            // Generate basic HTML structure for file-based templates
            $html = $this->generateBasicHtmlStructure($template);
        }

        // Replace placeholders with sample data
        foreach ($data as $key => $value) {
            $html = str_replace("{{$key}}", $value, $html);
        }

        return $html;
    }

    /**
     * Generate basic HTML structure for file-based templates.
     */
    private function generateBasicHtmlStructure(CertificateTemplate $template): string
    {
        $backgroundUrl = $template->background_data ? 
            Storage::url($template->background_data) : '';

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
                .certificate { 
                    position: relative; 
                    width: 800px; 
                    height: 600px; 
                    background-image: url('{$backgroundUrl}');
                    background-size: cover;
                    background-position: center;
                }
                .content { 
                    position: absolute; 
                    top: 0; 
                    left: 0; 
                    right: 0; 
                    bottom: 0; 
                    display: flex; 
                    flex-direction: column; 
                    justify-content: center; 
                    align-items: center; 
                    text-align: center; 
                    color: #000;
                }
            </style>
        </head>
        <body>
            <div class='certificate'>
                <div class='content'>
                    <h1>{{user_name}}</h1>
                    <h2>{{course_title}}</h2>
                    <p>{{certificate_date}}</p>
                    <p>{{certificate_id}}</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
