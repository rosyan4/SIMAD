<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'opd_unit_id',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Constants
    const ROLE_ADMIN_UTAMA = 'admin_utama';
    const ROLE_ADMIN_OPD = 'admin_opd';
    const ROLES = [self::ROLE_ADMIN_UTAMA, self::ROLE_ADMIN_OPD];

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$id},user_id",
            'password' => $id ? 'nullable|string|min:8' : 'required|string|min:8',
            'role' => 'required|in:admin_utama,admin_opd',
            'opd_unit_id' => 'nullable|exists:opd_units,opd_unit_id',
        ];
    }

    // Helpers
    public function isAdminUtama()
    {
        return $this->role === self::ROLE_ADMIN_UTAMA;
    }

    public function isAdminOPD()
    {
        return $this->role === self::ROLE_ADMIN_OPD;
    }

    public function updateLastLogin()
    {
        $this->update([
            'last_login' => now()
        ]);
    }

    // Accessors
    public function getDisplayRoleAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN_UTAMA => 'Admin Utama',
            self::ROLE_ADMIN_OPD => 'Admin OPD',
            default => 'Unknown Role'
        };
    }

    public function getOpdUnitNameAttribute()
    {
        return $this->opdUnit ? $this->opdUnit->nama_opd : '-';
    }

    // Relationships
    public function opdUnit()
    {
        return $this->belongsTo(OpdUnit::class, 'opd_unit_id');
    }

    public function createdAssets()
    {
        return $this->hasMany(Asset::class, 'created_by');
    }

    // Scopes
    public function scopeAdminUtama($query)
    {
        return $query->where('role', self::ROLE_ADMIN_UTAMA);
    }

    public function scopeAdminOPD($query)
    {
        return $query->where('role', self::ROLE_ADMIN_OPD);
    }

    public function scopeByOpdUnit($query, $opdUnitId)
    {
        return $query->where('opd_unit_id', $opdUnitId);
    }
}