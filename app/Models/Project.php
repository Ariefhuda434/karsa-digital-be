<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'category',
        'description',
        'client_name',
        'duration_weeks',
        'bg_color',
        'image_path',
        'result_stats',
        'tech_stack',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'result_stats' => 'array',
        'tech_stack'   => 'array',
        'is_featured'  => 'boolean',
    ];

    // URL gambar siap pakai di frontend
    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        return asset('storage/' . $this->image_path);
    }
}