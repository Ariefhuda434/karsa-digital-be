<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use App\Models\Project;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Project::orderBy('sort_order')->orderBy('created_at', 'desc');
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }
        return response()->json($query->get());
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json($project);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'subtitle'       => 'nullable|string|max:255',
            'category'       => 'required|string',
            'description'    => 'nullable|string',
            'client_name'    => 'nullable|string|max:255',
            'duration_weeks' => 'nullable|integer',
            'bg_color'       => 'nullable|string|max:20',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'result_stats'   => 'nullable|json',
            'tech_stack'     => 'nullable',
            'is_featured'    => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $url = $this->cloudinary->upload($request->file('image'), 'karsa/projects');
            if (!$url) {
                return response()->json(['message' => 'Gagal upload gambar ke Cloudinary.'], 500);
            }
            $validated['image_path'] = $url;
        }

        if (isset($validated['tech_stack']) && is_string($validated['tech_stack'])) {
            $validated['tech_stack'] = array_filter(array_map('trim', explode(',', $validated['tech_stack'])));
        }

        if (isset($validated['result_stats']) && is_string($validated['result_stats'])) {
            $validated['result_stats'] = json_decode($validated['result_stats'], true);
        }

        unset($validated['image']);
        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'title'          => 'sometimes|required|string|max:255',
            'subtitle'       => 'nullable|string|max:255',
            'category'       => 'sometimes|required|string',
            'description'    => 'nullable|string',
            'client_name'    => 'nullable|string|max:255',
            'duration_weeks' => 'nullable|integer',
            'bg_color'       => 'nullable|string|max:20',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'result_stats'   => 'nullable|json',
            'tech_stack'     => 'nullable|string',
            'is_featured'    => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            if ($project->image_path) {
                $this->cloudinary->delete($project->image_path);
            }
            $url = $this->cloudinary->upload($request->file('image'), 'karsa/projects');
            if (!$url) {
                return response()->json(['message' => 'Gagal upload gambar ke Cloudinary.'], 500);
            }
            $validated['image_path'] = $url;
        }

        if (isset($validated['tech_stack']) && is_string($validated['tech_stack'])) {
            $validated['tech_stack'] = array_filter(array_map('trim', explode(',', $validated['tech_stack'])));
        }

        if (isset($validated['result_stats']) && is_string($validated['result_stats'])) {
            $validated['result_stats'] = json_decode($validated['result_stats'], true);
        }

        unset($validated['image']);
        $project->update($validated);

        return response()->json($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        if ($project->image_path) {
            $this->cloudinary->delete($project->image_path);
        }
        $project->delete();
        return response()->json(['message' => 'Proyek berhasil dihapus.']);
    }
}