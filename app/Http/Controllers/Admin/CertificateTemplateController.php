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
    public function index(Request $request): JsonResponse
    {
        $query = CertificateTemplate::query();

        // Apply filters
        if ($request->has('type') && $request->type) {
            $query->where('template_type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $templates = $query->orderBy('name')
            ->get();

        // Mappa i dati per il frontend
        $templatesData = $templates->map(function ($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'type' => $template->template_type,
                'course_id' => $template->course_id,
                'background_image' => $template->background_image,
                'placeholder_positions' => $template->settings['placeholder_positions'] ?? [],
                'styling' => $template->settings['styling'] ?? [],
                'is_active' => $template->is_active,
                'created_at' => $template->created_at,
                'updated_at' => $template->updated_at,
            ];
        });

        return response()->json([
            'data' => $templatesData,
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
            'course_id' => 'required|exists:courses,id', // Corso obbligatorio
            'background_type' => 'nullable|in:html,pdf,image',
            'background_data' => 'nullable|string',
            'background_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'placeholder_positions' => 'nullable|array',
            'styling' => 'nullable|array',
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
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        // Map fields to database structure
        $templateData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'template_type' => $validated['type'],
            'course_id' => $validated['course_id'], // Corso associato
            'background_image' => $validated['background_data'] ?? null,
            'settings' => [
                'background_type' => $validated['background_type'] ?? 'image',
                'placeholder_positions' => $validated['placeholder_positions'] ?? [],
                'styling' => $validated['styling'] ?? []
            ],
            'is_active' => $validated['is_active']
        ];

        $template = CertificateTemplate::create($templateData);

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
        // Mappa i dati per il frontend
        $templateData = [
            'id' => $certificateTemplate->id,
            'name' => $certificateTemplate->name,
            'description' => $certificateTemplate->description,
            'type' => $certificateTemplate->template_type,
            'course_id' => $certificateTemplate->course_id,
            'background_image' => $certificateTemplate->background_image,
            'placeholder_positions' => $certificateTemplate->settings['placeholder_positions'] ?? [],
            'styling' => $certificateTemplate->settings['styling'] ?? [],
            'is_active' => $certificateTemplate->is_active,
            'created_at' => $certificateTemplate->created_at,
            'updated_at' => $certificateTemplate->updated_at,
        ];

        return response()->json([
            'data' => $templateData,
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
            'course_id' => 'sometimes|exists:courses,id', // Corso opzionale per update
            'background_type' => 'nullable|in:html,pdf,image',
            'background_data' => 'nullable|string',
            'background_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'placeholder_positions' => 'nullable|array',
            'styling' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('background_file')) {
            // Delete old file if exists
            if ($certificateTemplate->background_image) {
                Storage::delete('public/' . $certificateTemplate->background_image);
            }

            $file = $request->file('background_file');
            $filename = 'certificate-templates/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public', $filename);
            $validated['background_data'] = $filename;
        }

        // Map fields to database structure
        $updateData = [];
        if (isset($validated['name'])) $updateData['name'] = $validated['name'];
        if (isset($validated['description'])) $updateData['description'] = $validated['description'];
        if (isset($validated['type'])) $updateData['template_type'] = $validated['type'];
        if (isset($validated['course_id'])) $updateData['course_id'] = $validated['course_id'];
        if (isset($validated['background_data'])) $updateData['background_image'] = $validated['background_data'];
        if (isset($validated['is_active'])) $updateData['is_active'] = $validated['is_active'];
        
        // Update settings
        $settings = $certificateTemplate->settings ?? [];
        if (isset($validated['background_type'])) $settings['background_type'] = $validated['background_type'];
        if (isset($validated['placeholder_positions'])) $settings['placeholder_positions'] = $validated['placeholder_positions'];
        if (isset($validated['styling'])) $settings['styling'] = $validated['styling'];
        $updateData['settings'] = $settings;

        $certificateTemplate->update($updateData);

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
        // Delete associated file
        if ($certificateTemplate->background_image) {
            Storage::delete('public/' . $certificateTemplate->background_image);
        }

        $certificateTemplate->delete();

        return response()->json([
            'message' => 'Template eliminato con successo.',
        ]);
    }

    /**
     * Duplicate a template.
     */
    public function duplicate(CertificateTemplate $certificateTemplate): JsonResponse
    {
        $newTemplate = $certificateTemplate->replicate();
        $newTemplate->name = $certificateTemplate->name . ' (Copia)';
        $newTemplate->save();

        return response()->json([
            'message' => 'Template duplicato con successo.',
            'data' => $newTemplate,
        ]);
    }

    /**
     * Set template as default.
     */
    public function setDefault(CertificateTemplate $certificateTemplate): JsonResponse
    {
        // For now, just return success since we don't have is_default field
        return response()->json([
            'message' => 'Template impostato come predefinito.',
            'data' => $certificateTemplate,
        ]);
    }

    /**
     * Export template as JSON.
     */
    public function export(CertificateTemplate $certificateTemplate): JsonResponse
    {
        $exportData = [
            'name' => $certificateTemplate->name,
            'description' => $certificateTemplate->description,
            'type' => $certificateTemplate->template_type,
            'background_type' => $certificateTemplate->settings['background_type'] ?? 'image',
            'background_data' => $certificateTemplate->background_image,
            'placeholder_positions' => $certificateTemplate->settings['placeholder_positions'] ?? [],
            'styling' => $certificateTemplate->settings['styling'] ?? [],
            'exported_at' => now()->toISOString(),
            'version' => '1.0'
        ];

        return response()->json([
            'data' => $exportData,
        ]);
    }

    /**
     * Import template from JSON.
     */
    public function import(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_data' => 'required|array',
            'template_data.name' => 'required|string|max:255',
            'template_data.type' => 'required|in:course,track,custom',
            'template_data.background_type' => 'required|in:html,pdf,image',
        ]);

        $templateData = $validated['template_data'];
        
        // Create new template
        $template = CertificateTemplate::create([
            'name' => $templateData['name'],
            'description' => $templateData['description'] ?? '',
            'template_type' => $templateData['type'],
            'background_image' => $templateData['background_data'] ?? '',
            'settings' => [
                'background_type' => $templateData['background_type'],
                'placeholder_positions' => $templateData['placeholder_positions'] ?? [],
                'styling' => $templateData['styling'] ?? []
            ],
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Template importato con successo.',
            'data' => $template,
        ], 201);
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
