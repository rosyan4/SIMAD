<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditReport extends Model
{
    use HasFactory;

    protected $primaryKey = 'audit_id';
    
    protected $fillable = [
        'asset_id',
        'auditor_id',
        'findings',
        'audit_date',
        'status',
        'report_file_path',
        'follow_up',
        'follow_up_deadline',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'follow_up_deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants - DIKOREKSI SESUAI MIGRATION
    const STATUS_DALAM_PROSES = 'dalam_proses';
    const STATUS_SELESAI = 'selesai';
    const STATUS_PERLU_TINDAK_LANJUT = 'perlu_tindak_lanjut';

    const STATUSES = [
        self::STATUS_DALAM_PROSES,
        self::STATUS_SELESAI,
        self::STATUS_PERLU_TINDAK_LANJUT
    ];

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'asset_id' => 'required|exists:assets,asset_id',
            'auditor_id' => 'required|exists:users,user_id',
            'findings' => 'required|string|min:10|max:5000',
            'audit_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:' . implode(',', self::STATUSES),
            'report_file_path' => 'nullable|string|max:500',
            'follow_up' => 'nullable|string|max:1000',
            'follow_up_deadline' => 'nullable|date|after_or_equal:audit_date',
        ];
    }

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByAuditor($query, $auditorId)
    {
        return $query->where('auditor_id', $auditorId);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_DALAM_PROSES);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    public function scopeNeedFollowUp($query)
    {
        return $query->where('status', self::STATUS_PERLU_TINDAK_LANJUT);
    }

    // Status helpers
    public function isInProgress()
    {
        return $this->status === self::STATUS_DALAM_PROSES;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function needsFollowUp()
    {
        return $this->status === self::STATUS_PERLU_TINDAK_LANJUT;
    }

    // Accessors
    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            self::STATUS_DALAM_PROSES => 'Dalam Proses',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_PERLU_TINDAK_LANJUT => 'Perlu Tindak Lanjut',
            default => 'Status Tidak Dikenal'
        };
    }

    public function getFindingsSummaryAttribute()
    {
        if (strlen($this->findings) <= 100) {
            return $this->findings;
        }

        return substr($this->findings, 0, 97) . '...';
    }
}