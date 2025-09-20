<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Farmer extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tblFarmers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'farmerID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'farmerName',
        'username',
        'password',
        'farmerLocation',
        'farmerContactInfo',
        'farmerCooperative',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Scope a query to only include active farmers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by location.
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('farmerLocation', 'like', '%' . $location . '%');
    }

    /**
     * Scope a query to filter by cooperative.
     */
    public function scopeByCooperative($query, $cooperative)
    {
        return $query->where('farmerCooperative', 'like', '%' . $cooperative . '%');
    }

    /**
     * Scope a query to search farmers by name.
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where('farmerName', 'like', '%' . $name . '%');
    }

    /**
     * Get the name attribute for compatibility with views.
     */
    public function getNameAttribute()
    {
        return $this->farmerName;
    }

    /**
     * Get the municipality attribute for compatibility with views.
     */
    public function getMunicipalityAttribute()
    {
        return $this->farmerLocation;
    }

    /**
     * Get the cooperative attribute for compatibility with views.
     */
    public function getCooperativeAttribute()
    {
        return $this->farmerCooperative;
    }

    /**
     * Get the contact_number attribute for compatibility with views.
     */
    public function getContactNumberAttribute()
    {
        return $this->farmerContactInfo;
    }

    /**
     * Get the email attribute for compatibility with views.
     */
    public function getEmailAttribute()
    {
        return null; // Farmers don't have email in current schema
    }

    /**
     * Get the crops for the farmer.
     */
    public function crops()
    {
        return $this->hasMany(Crop::class, 'farmer_id', 'farmerID');
    }
}