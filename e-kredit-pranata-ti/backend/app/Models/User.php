<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip',
        'nip_sso',
        'sso_uid',
        'name',
        'email',
        'password',
        'role',
        'position',
        'unit_kerja',
        'jenjang_jabatan',
        'golongan',
        'affiliation',
        'auth_method',
        'sso_last_login',
        'target_angka_kredit',
        'angka_kredit_minimal',
        // Credit tracking fields
        'current_credit_utama',
        'current_credit_penunjang',
        'current_credit_total',
        'banked_credit_utama',
        'banked_credit_penunjang',
        'banked_credit_total',
        'compliance_percentage_utama',
        'compliance_percentage_penunjang',
        'progress_percentage',
        'is_compliant',
        'last_credit_updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sso_last_login' => 'datetime',
            'target_angka_kredit' => 'decimal:2',
            'angka_kredit_minimal' => 'decimal:2',
            'current_credit_utama' => 'decimal:2',
            'current_credit_penunjang' => 'decimal:2',
            'current_credit_total' => 'decimal:2',
            'banked_credit_utama' => 'decimal:2',
            'banked_credit_penunjang' => 'decimal:2',
            'banked_credit_total' => 'decimal:2',
            'compliance_percentage_utama' => 'decimal:2',
            'compliance_percentage_penunjang' => 'decimal:2',
            'progress_percentage' => 'decimal:2',
            'is_compliant' => 'boolean',
            'last_credit_updated_at' => 'datetime',
        ];
    }

    /**
     * Get activities submitted by this user
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get approvals made by this user (if verifier)
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'verifier_id');
    }

    /**
     * Get SKP (Sasaran Kerja Pegawai) for this user
     */
    public function skps()
    {
        return $this->hasMany(Skp::class);
    }

    /**
     * Get credit banks for this user
     */
    public function creditBanks()
    {
        return $this->hasMany(CreditBank::class);
    }

    /**
     * Get banked credits only
     */
    public function bankedCredits()
    {
        return $this->creditBanks()->where('status', 'banked');
    }

    /**
     * Get compliance summary
     */
    public function getComplianceSummary(): array
    {
        return [
            'current_credits' => [
                'utama' => $this->current_credit_utama ?? 0,
                'penunjang' => $this->current_credit_penunjang ?? 0,
                'total' => $this->current_credit_total ?? 0,
            ],
            'banked_credits' => [
                'utama' => $this->banked_credit_utama ?? 0,
                'penunjang' => $this->banked_credit_penunjang ?? 0,
                'total' => $this->banked_credit_total ?? 0,
            ],
            'compliance' => [
                'percentage_utama' => $this->compliance_percentage_utama ?? 0,
                'percentage_penunjang' => $this->compliance_percentage_penunjang ?? 0,
                'is_compliant' => $this->is_compliant ?? true,
            ],
            'progress' => [
                'target' => $this->target_angka_kredit ?? 0,
                'current' => $this->current_credit_total ?? 0,
                'percentage' => $this->progress_percentage ?? 0,
            ],
            'position' => [
                'jenjang' => $this->jenjang_jabatan,
                'golongan' => $this->golongan,
            ],
        ];
    }

    /**
     * Get SKP that this user approved
     */
    public function approvedSkps()
    {
        return $this->hasMany(Skp::class, 'approved_by');
    }
}
