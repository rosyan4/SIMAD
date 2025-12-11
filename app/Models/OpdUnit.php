<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpdUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'opd_unit_id';
    
    protected $fillable = [
        'kode_opd',
        'kode_opd_numeric',
        'nama_opd',
        'alamat',
        'kepala_opd',
        'nip_kepala_opd',
    ];

    protected $casts = [
        'kode_opd_numeric' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'kode_opd' => "required|string|max:10|unique:opd_units,kode_opd,{$id},opd_unit_id",
            'kode_opd_numeric' => "required|integer|between:1,99|unique:opd_units,kode_opd_numeric,{$id},opd_unit_id",
            'nama_opd' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kepala_opd' => 'nullable|string|max:255',
            'nip_kepala_opd' => 'nullable|string|max:20',
        ];
    }

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class, 'opd_unit_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'opd_unit_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'opd_unit_id');
    }

    // Scopes
    public function scopeByNumericCode($query, $kode)
    {
        return $query->where('kode_opd_numeric', $kode);
    }

    // Accessors
    public function getAssetCountAttribute()
    {
        return $this->assets()->count();
    }

    public function getTotalAssetValueAttribute()
    {
        return $this->assets()->sum('value');
    }

    public function getFormattedTotalAssetValueAttribute()
    {
        return 'Rp ' . number_format($this->total_asset_value, 0, ',', '.');
    }
}