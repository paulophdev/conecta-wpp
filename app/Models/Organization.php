<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'max_connections',
        'api_key',
        'user_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function connections()
    {
        return $this->hasMany(Connection::class);
    }
}
