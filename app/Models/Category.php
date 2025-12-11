<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';
    
    protected $fillable = [
        'name',
        'description',
        'standard_code_ref',
        'kib_code',
        'sub_categories',
    ];

    protected $casts = [
        'sub_categories' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const KIB_CODES = ['A', 'B', 'C', 'D', 'E', 'F'];

    /**
     * Boot method for validation
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!in_array($model->kib_code, self::KIB_CODES)) {
                throw new \InvalidArgumentException('Kode KIB tidak valid');
            }
        });
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'kib_code' => 'required|in:A,B,C,D,E,F',
            'standard_code_ref' => "required|string|max:50|unique:categories,standard_code_ref,{$id},category_id",
            'sub_categories' => 'nullable|array',
            'sub_categories.*' => 'required|string',
        ];
    }

    // Relationships
    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    // Scopes
    public function scopeKibCode($query, $code)
    {
        return $query->where('kib_code', $code);
    }

    // Accessors
    public function getFormattedSubCategoriesAttribute()
    {
        if (empty($this->sub_categories)) {
            return [];
        }

        $formatted = [];
        foreach ($this->sub_categories as $code => $name) {
            $formatted[] = [
                'code' => $code,
                'name' => $name
            ];
        }

        return $formatted;
    }

    public function getAssetCountAttribute()
    {
        return $this->assets()->count();
    }

    public function getTotalAssetValueAttribute()
    {
        return $this->assets()->sum('value');
    }
}