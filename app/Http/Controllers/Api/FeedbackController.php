<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        return response()->json(Feedback::where('is_featured', true)->latest()->get());
    }

    public function adminIndex()
    {
        return response()->json(Feedback::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string',
            'role'            => 'nullable|string',
            'company'         => 'nullable|string',
            'avatar_initials' => 'required|string|max:5',
            'avatar_color'    => 'nullable|string',
            'content'         => 'required|string',
            'rating'          => 'nullable|integer|min:1|max:5',
            'project_name'    => 'nullable|string',
        ]);
        return response()->json(Feedback::create($data), 201);
    }

    public function show(Feedback $feedback)
    {
        return response()->json($feedback);
    }

    public function update(Request $request, Feedback $feedback)
    {
        $feedback->update($request->only(['is_featured', 'rating', 'content']));
        return response()->json($feedback);
    }
}
