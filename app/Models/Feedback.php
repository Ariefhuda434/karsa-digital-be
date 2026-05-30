<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks'; 
    protected $fillable = [
        'name',
        'role',
        'company',
        'avatar_initials',
        'avatar_color',
        'content',
        'rating',
        'project_name',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'rating'      => 'integer',
    ];
}