<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    protected $fillable = [
        'name',
        'webhook_url',
        'public_token',
        'is_active',
        'webhook_enable',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}