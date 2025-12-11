<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'asset_id';
    
    protected $fillable = [
        'asset_code',
        'asset_code_old',
        'name',
        'category_id',
        'sub_category_code',
        'location_id',
        'value',
        'acquisition_year',
        'status',
        'condition',
        'document_verification_status',
        'validation_status',
        'kib_data',
        'created_by',
        'opd_unit_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'acquisition_year' => 'integer',
        'kib_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constants
    const STATUSES = ['aktif', 'dimutasi', 'dihapus', 'dalam_perbaikan', 'nonaktif'];
    const CONDITIONS = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
    const DOCUMENT_VERIFICATION_STATUSES = ['belum_diverifikasi', 'valid', 'tidak_valid'];
    const VALIDATION_STATUSES = ['belum_divalidasi', 'disetujui', 'revisi', 'ditolak'];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Event: created
        static::created(function ($model) {
            $model->createHistoryRecord('create', 'Aset baru ditambahkan');
        });

        // Event: updated
        static::updated(function ($model) {
            $changes = $model->getChanges();
            if (!empty($changes)) {
                $model->createHistoryRecord(
                    'update',
                    'Data aset diperbarui'
                );
            }
        });

        // Event: deleted (soft delete)
        static::deleted(function ($model) {
            if (!$model->isForceDeleting()) {
                $model->createHistoryRecord('delete', 'Aset dihapus (soft delete)');
            }
        });

        // Event: restored
        static::restored(function ($model) {
            $model->createHistoryRecord('restore', 'Aset dikembalikan dari penghapusan');
        });
    }

    /**
     * Create history record for any asset change
     */
    protected function createHistoryRecord(string $action, string $description): void
    {
        $this->histories()->create([
            'action' => $action,
            'description' => $description,
            'change_by' => Auth::id() ?? $this->created_by,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Validation rules for asset - DIKOREKSI LENGKAP
     */
    public static function rules($id = null)
    {
        $uniqueRule = $id ? "unique:assets,asset_code,{$id},asset_id" : 'unique:assets,asset_code';
        
        return [
            'asset_code' => "nullable|string|max:50|{$uniqueRule}",
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'sub_category_code' => 'required|string|max:10',
            'location_id' => 'nullable|exists:locations,location_id',
            'value' => 'required|numeric|min:0',
            'acquisition_year' => 'required|integer|min:1900|max:' . date('Y'),
            'status' => 'required|in:' . implode(',', self::STATUSES),
            'condition' => 'required|in:' . implode(',', self::CONDITIONS),
            'document_verification_status' => 'required|in:' . 
                implode(',', self::DOCUMENT_VERIFICATION_STATUSES),
            'validation_status' => 'required|in:' . 
                implode(',', self::VALIDATION_STATUSES),
            'opd_unit_id' => 'required|exists:opd_units,opd_unit_id',
            'created_by' => 'required|exists:users,user_id',
            'kib_data' => 'nullable|array',
        ];
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function opdUnit()
    {
        return $this->belongsTo(OpdUnit::class, 'opd_unit_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'asset_id');
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class, 'asset_id');
    }

    public function mutations()
    {
        return $this->hasMany(AssetMutation::class, 'asset_id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'asset_id');
    }

    public function depreciations()
    {
        return $this->hasMany(Depreciation::class, 'asset_id');
    }

    public function deletions()
    {
        return $this->hasMany(AssetDeletion::class, 'asset_id');
    }

    public function audits()
    {
        return $this->hasMany(AuditReport::class, 'asset_id');
    }

    // Accessors
    public function getSubCategoryNameAttribute()
    {
        $subCategories = $this->category->sub_categories ?? [];
        return $subCategories[$this->sub_category_code] ?? $this->sub_category_code;
    }

    public function getFormattedValueAttribute()
    {
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    // Scopes
    public function scopeByOpdUnit($query, $opdUnitId)
    {
        return $query->where('opd_unit_id', $opdUnitId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeVerified($query)
    {
        return $query->where('document_verification_status', 'valid')
                     ->where('validation_status', 'disetujui');
    }
}