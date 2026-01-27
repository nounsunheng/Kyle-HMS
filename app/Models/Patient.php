<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact',
        'medical_history',
        'blood_type',
        'allergies',
        'profile_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * Accessors
     */

    public function getAgeAttribute()
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getFullNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * Scopes
     */

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->whereNotNull('email_verified_at');
        });
    }

    /**
     * Get profile image URL
     */

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }

        // Default avatar based on gender
        $initial = strtoupper(substr($this->user->name, 0, 1));
        return "https://ui-avatars.com/api/?name={$initial}&size=200&background=0066CC&color=fff";
    }
}
