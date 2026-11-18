<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function cutis(): HasMany
    {
        return $this->hasMany(Cuti::class);
    }

    protected $fillable = [
        'name',
        'email',
        'no_wa',
        'foto_profile',
        'password',
        'role',
        'jatah_cuti_tahunan',
        'sisa_cuti_tahunan',
        'login_attempts',
        'is_locked',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_locked' => 'boolean',
    ];
}
