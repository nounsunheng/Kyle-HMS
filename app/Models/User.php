<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'usertype',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
     * Relationships
     */

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Helper methods
     */

    public function isPatient()
    {
        return $this->usertype === 'patient' || $this->hasRole('patient');
    }

    public function isDoctor()
    {
        return $this->usertype === 'doctor' || $this->hasRole('doctor');
    }

    public function isAdmin()
    {
        return $this->usertype === 'admin' || $this->hasRole('admin');
    }

    /**
     * Get user's avatar URL
     * This accessor provides a unified way to get avatar across all user types
     */
    public function getAvatarUrlAttribute(): string
    {
        // Patient avatar
        if ($this->patient) {
            return $this->patient->profile_image_url;
        }

        // Doctor avatar
        if ($this->doctor) {
            return $this->doctor->profile_image_url;
        }

        // Admin avatar
        if ($this->admin) {
            return $this->admin->profile_image_url;
        }

        // Fallback default avatar
        $initial = strtoupper(substr($this->name, 0, 1));
        return "https://ui-avatars.com/api/?name={$initial}&size=500&background=6b7280&color=fff&bold=true";
    }

    /**
     * Get user's full name with title (for doctors)
     */
    public function getFullNameAttribute(): string
    {
        if ($this->doctor) {
            return 'Dr. ' . $this->name;
        }

        return $this->name;
    }

    /**
     * Get user's role name
     */
    public function getRoleNameAttribute(): string
    {
        if ($this->isAdmin()) {
            return 'Administrator';
        }

        if ($this->isDoctor()) {
            return 'Doctor';
        }

        if ($this->isPatient()) {
            return 'Patient';
        }

        return 'User';
    }
}
