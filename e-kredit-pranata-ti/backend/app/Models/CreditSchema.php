<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditSchema extends Model
{
    protected $table = 'credit_schema';

    protected $fillable = [
        'category',
        'subcategory',
        'activity_name',
        'credit_points',
        'description',
        'satuan_hasil',
        'batasan_penilaian',
        'pelaksana',
        'bukti_fisik',
        'unsur_type',
    ];

    protected function casts(): array
    {
        return [
            'credit_points' => 'decimal:3',
        ];
    }

    /**
     * Get activities that use this credit schema
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'schema_id');
    }

    /**
     * Get SKP items that use this credit schema
     */
    public function skpItems()
    {
        return $this->hasMany(SkpItem::class, 'schema_id');
    }

    /**
     * Check if user can perform this activity based on jenjang jabatan
     */
    public function canBePerformedBy($jenjangJabatan): bool
    {
        // If pelaksana is null or "Semua Jenjang", anyone can perform it
        if (!$this->pelaksana || $this->pelaksana === 'Semua Jenjang') {
            return true;
        }

        // Check if user's jenjang matches the required pelaksana
        return $this->pelaksana === $jenjangJabatan;
    }

    /**
     * Get batasan penilaian as a numeric value (if applicable)
     * For example: "25 program/tahun" returns 25
     */
    public function getBatasanNumericAttribute(): ?int
    {
        if (!$this->batasan_penilaian || $this->batasan_penilaian === 'tidak terbatas') {
            return null;
        }

        // Extract number from string like "25 program/tahun"
        preg_match('/(\d+)/', $this->batasan_penilaian, $matches);
        return isset($matches[1]) ? (int) $matches[1] : null;
    }
}
