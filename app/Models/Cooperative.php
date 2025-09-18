<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cooperative extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'municipality',
        'address',
        'contact_person',
        'contact_number',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function farmers()
    {
        return $this->hasMany(User::class)->where('role_id', 1);
    }
}
