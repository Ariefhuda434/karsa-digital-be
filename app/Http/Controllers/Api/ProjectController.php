<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    // GET /api/projects
    public function index(): JsonResponse
    {
        $projects = Project::orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        return response()->json($projects);
    }

    // GET /api/projects/{id}
    public function show(Project $project): JsonResponse
    {
        return response()->json($project);
    }

    // POST /api/projects  (admin)
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
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'result_stats'   => 'nullable|json',
            'tech_stack'     => 'nullable',
            'is_featured'    => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
        ]);

        // Handle upload gambar
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('projects', 'public');
        }

        // Parse tech_stack dari string ke array
        if (isset($validated['tech_stack']) && is_string($validated['tech_stack'])) {
            $validated['tech_stack'] = array_filter(array_map('trim', explode(',', $validated['tech_stack'])));
        }

        // Parse result_stats dari JSON string
        if (isset($validated['result_stats']) && is_string($validated['result_stats'])) {
            $validated['result_stats'] = json_decode($validated['result_stats'], true);
        }

        unset($validated['image']);
        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    // PUT /api/projects/{id}  (admin)
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
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'result_stats'   => 'nullable|json',
            'tech_stack'     => 'nullable|string',
            'is_featured'    => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
        ]);

        // Handle upload gambar baru — hapus yang lama
        if ($request->hasFile('image')) {
            if ($project->image_path) {
                Storage::disk('public')->delete($project->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('projects', 'public');
        }

        // Parse tech_stack
        if (isset($validated['tech_stack']) && is_string($validated['tech_stack'])) {
            $validated['tech_stack'] = array_filter(array_map('trim', explode(',', $validated['tech_stack'])));
        }

        // Parse result_stats
        if (isset($validated['result_stats']) && is_string($validated['result_stats'])) {
            $validated['result_stats'] = json_decode($validated['result_stats'], true);
        }

        unset($validated['image']);
        $project->update($validated);

        return response()->json($project);
    }

    // DELETE /api/projects/{id}  (admin)
    public function destroy(Project $project): JsonResponse
    {
        if ($project->image_path) {
            Storage::disk('public')->delete($project->image_path);
        }
        $project->delete();
        return response()->json(['message' => 'Proyek berhasil dihapus.']);
    }
}