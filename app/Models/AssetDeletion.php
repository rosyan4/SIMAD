<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDeletion extends Model
{
    use HasFactory;

    protected $primaryKey = 'deletion_id';
    
    protected $fillable = [
        'asset_id',
        'deletion_reason',
        'reason_details',
        'status',
        'proposed_by',
        'verified_by',
        'approved_by',
        'proposed_at',
        'verified_at',
        'approved_at',
        'deleted_at',
        'proposal_documents',
        'approval_documents',
        'deletion_method',
        'sale_value',
        'recipient',
        'notes',
    ];

    protected $casts = [
        'proposed_at' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'deleted_at' => 'datetime',
        'proposal_documents' => 'array',
        'approval_documents' => 'array',
        'sale_value' => 'decimal:2',
    ];

    // Status constants - DIKOREKSI SESUAI MIGRATION
    const STATUS_DIUSULKAN = 'diusulkan';
    const STATUS_DIVERIFIKASI = 'diverifikasi';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_SELESAI = 'selesai';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_DIBATALKAN = 'dibatalkan';

    const STATUSES = [
        self::STATUS_DIUSULKAN,
        self::STATUS_DIVERIFIKASI,
        self::STATUS_DISETUJUI,
        self::STATUS_SELESAI,
        self::STATUS_DITOLAK,
        self::STATUS_DIBATALKAN
    ];

    // Deletion reasons constants
    const REASON_RUSAK_BERAT = 'rusak_berat';
    const REASON_HILANG = 'hilang';
    const REASON_JUAL = 'jual';
    const REASON_HIBAH = 'hibah';
    const REASON_MUSNAH = 'musnah';
    const REASON_LAINNYA = 'lainnya';

    const DELETION_REASONS = [
        self::REASON_RUSAK_BERAT,
        self::REASON_HILANG,
        self::REASON_JUAL,
        self::REASON_HIBAH,
        self::REASON_MUSNAH,
        self::REASON_LAINNYA
    ];

    // Deletion methods constants
    const METHOD_JUAL = 'jual';
    const METHOD_HIBAH = 'hibah';
    const METHOD_MUSNAH = 'musnah';
    const METHOD_SCRAP = 'scrap';

    const DELETION_METHODS = [
        self::METHOD_JUAL,
        self::METHOD_HIBAH,
        self::METHOD_MUSNAH,
        self::METHOD_SCRAP
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($model) {
            if ($model->isDirty('status') && $model->status === self::STATUS_SELESAI) {
                $model->asset->update(['status' => 'dihapus']);
            }
        });
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'asset_id' => 'required|exists:assets,asset_id',
            'deletion_reason' => 'required|in:' . implode(',', self::DELETION_REASONS),
            'reason_details' => 'required|string|min:10|max:1000',
            'status' => 'required|in:' . implode(',', self::STATUSES),
            'deletion_method' => 'nullable|in:' . implode(',', self::DELETION_METHODS),
            'sale_value' => 'nullable|numeric|min:0',
            'recipient' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'proposal_documents' => 'nullable|array',
            'proposal_documents.*' => 'nullable|string',
            'approval_documents' => 'nullable|array',
            'approval_documents.*' => 'nullable|string',
        ];
    }

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_DIUSULKAN);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_DIVERIFIKASI);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_DIBATALKAN);
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('deletion_reason', $reason);
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === self::STATUS_DIUSULKAN;
    }

    public function isVerified()
    {
        return $this->status === self::STATUS_DIVERIFIKASI;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_DIBATALKAN;
    }

    // Accessors
    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            self::STATUS_DIUSULKAN => 'Diusulkan',
            self::STATUS_DIVERIFIKASI => 'Diverifikasi',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
            default => 'Status Tidak Dikenal'
        };
    }

    public function getDeletionReasonDisplayAttribute()
    {
        return match($this->deletion_reason) {
            self::REASON_RUSAK_BERAT => 'Rusak Berat',
            self::REASON_HILANG => 'Hilang',
            self::REASON_JUAL => 'Dijual',
            self::REASON_HIBAH => 'Dihibahkan',
            self::REASON_MUSNAH => 'Musnah',
            self::REASON_LAINNYA => 'Lainnya',
            default => 'Alasan Tidak Dikenal'
        };
    }

    public function getFormattedSaleValueAttribute()
    {
        return $this->sale_value ? 'Rp ' . 
            number_format($this->sale_value, 0, ',', '.') : '-';
    }
}