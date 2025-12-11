<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';
    
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'opd_unit_id',
        'type',
        'address',
    ];

    // Valid location types
    const TYPES = ['gedung', 'ruangan', 'gudang', 'lapangan', 'lainnya'];

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opd_unit_id' => 'required|exists:opd_units,opd_unit_id',
            'type' => 'required|in:' . implode(',', self::TYPES),
            'address' => 'nullable|string',
        ];
    }

    // Accessors
    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->latitude}, {$this->longitude}";
        }
        return null;
    }

    public function getHasCoordinatesAttribute()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    // Relationships
    public function opdUnit()
    {
        return $this->belongsTo(OpdUnit::class, 'opd_unit_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'location_id');
    }

    // Scopes
    public function scopeOfOpd($query, $opdUnitId)
    {
        return $query->where('opd_unit_id', $opdUnitId);
    }

    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function getAssetCountAttribute()
    {
        return $this->assets()->count();
    }
}