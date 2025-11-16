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
        'name',
        'email',
        'password',
        'role',
        'position',
        'unit_kerja',
        'jenjang_jabatan',
        'golongan',
        'target_angka_kredit',
        'angka_kredit_minimal',
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
     * Get SKP that this user approved
     */
    public function approvedSkps()
    {
        return $this->hasMany(Skp::class, 'approved_by');
    }
}
