<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'specialty_id',
        'phone',
        'license_number',
        'qualifications',
        'years_of_experience',
        'bio',
        'profile_image',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
        'years_of_experience' => 'integer',
    ];

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * Get appointments through schedules
     */
    public function appointments()
    {
        return $this->hasManyThrough(Appointment::class, Schedule::class);
    }

    /**
     * Accessors
     */

    public function getFullNameAttribute()
    {
        return 'Dr. ' . $this->user->name;
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }

        // Default avatar
        $initial = strtoupper(substr($this->user->name, 0, 1));
        return "https://ui-avatars.com/api/?name={$initial}&size=200&background=00A86B&color=fff";
    }

    /**
     * Scopes
     */

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeBySpecialty($query, $specialtyId)
    {
        return $query->where('specialty_id', $specialtyId);
    }
}
