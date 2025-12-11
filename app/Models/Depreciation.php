<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depreciation extends Model
{
    use HasFactory;

    protected $primaryKey = 'depreciation_id';
    
    protected $fillable = [
        'asset_id',
        'year',
        'method',
        'beginning_value',
        'depreciation_rate',
        'depreciation_amount',
        'accumulated_depreciation',
        'ending_value',
        'useful_life',
        'remaining_life',
        'status',
        'calculated_by',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'beginning_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'depreciation_amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'ending_value' => 'decimal:2',
        'useful_life' => 'integer',
        'remaining_life' => 'integer',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function calculator()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByAsset($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopePendingVerification($query)
    {
        return $query->where('status', 'dihitung');
    }
}