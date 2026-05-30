<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type',
        'project_name',
        'description',
        'budget_range',
        'timeline',
        'client_name',
        'client_email',
        'client_phone',
        'company',
        'status',
        'admin_notes',
    ];
}