<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crop extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'farmer_id',
        'name',
        'variety',
        'planting_date',
        'expected_harvest_date',
        'actual_harvest_date',
        'area_hectares',
        'description',
        'status',
        'expected_yield_kg',
        'actual_yield_kg',
        // New fields for agricultural statistics data
        'municipality',
        'farm_type',
        'year',
        'crop_name',
        'area_planted',
        'area_harvested',
        'production_mt',
        'productivity_mt_ha',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'planting_date' => 'date',
        'expected_harvest_date' => 'date',
        'actual_harvest_date' => 'date',
        'area_hectares' => 'decimal:2',
        'expected_yield_kg' => 'decimal:2',
        'actual_yield_kg' => 'decimal:2',
    ];

    /**
     * Get the farmer that owns the crop.
     */
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id', 'farmerID');
    }
}
