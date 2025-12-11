<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'history_id';
    
    protected $fillable = [
        'asset_id',
        'action',
        'description',
        'old_value',
        'new_value',
        'change_date',
        'change_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'change_date' => 'datetime',
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'change_by');
    }
}