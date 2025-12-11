<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $primaryKey = 'maintenance_id';
    
    protected $fillable = [
        'asset_id',
        'maintenance_type',
        'title',
        'description',
        'scheduled_date',
        'actual_date',
        'status',
        'cost',
        'vendor',
        'vendor_contact',
        'supporting_documents',
        'recorded_by',
        'approved_by',
        'approved_at',
        'result_notes',
        'result_status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'actual_date' => 'date',
        'cost' => 'decimal:2',
        'supporting_documents' => 'array',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'dijadwalkan');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'dalam_pengerjaan');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'dijadwalkan')
                     ->whereDate('scheduled_date', '<', now());
    }
}