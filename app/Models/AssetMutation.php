<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMutation extends Model
{
    use HasFactory;

    protected $primaryKey = 'mutation_id';
    
    protected $fillable = [
        'asset_id',
        'from_opd_unit_id',
        'to_opd_unit_id',
        'from_location_id',
        'to_location_id',
        'status',
        'mutation_date',
        'mutated_by',
        'supporting_documents',
        'notes',
    ];

    protected $casts = [
        'mutation_date' => 'date',
        'supporting_documents' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function fromOpdUnit()
    {
        return $this->belongsTo(OpdUnit::class, 'from_opd_unit_id');
    }

    public function toOpdUnit()
    {
        return $this->belongsTo(OpdUnit::class, 'to_opd_unit_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function mutator()
    {
        return $this->belongsTo(User::class, 'mutated_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'diusulkan');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }
}