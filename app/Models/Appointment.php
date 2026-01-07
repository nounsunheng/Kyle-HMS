<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'schedule_id',
        'appointment_number',
        'appointment_time',
        'status',
        'reason',
        'notes',
    ];

    /**
     * Boot method to generate appointment number automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            if (!$appointment->appointment_number) {
                $appointment->appointment_number = self::generateAppointmentNumber();
            }
        });
    }

    /**
     * Relationships
     */

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    /**
     * Get doctor through schedule
     */
    public function doctor()
    {
        return $this->hasOneThrough(
            Doctor::class,
            Schedule::class,
            'id',
            'id',
            'schedule_id',
            'doctor_id'
        );
    }

    /**
     * Accessors
     */

    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->appointment_time)->format('g:i A');
    }

    public function getFullDateTimeAttribute()
    {
        return $this->schedule->schedule_date->format('F d, Y') . ' at ' . $this->formatted_time;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-info',
            'completed' => 'badge-success',
            'cancelled' => 'badge-error',
            'no_show' => 'badge-ghost',
            default => 'badge-neutral',
        };
    }

    /**
     * Scopes
     */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereHas('schedule', function ($q) {
            $q->where('schedule_date', '>=', now()->toDateString());
        })->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopePast($query)
    {
        return $query->whereHas('schedule', function ($q) {
            $q->where('schedule_date', '<', now()->toDateString());
        })->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Helper methods
     */

    public static function generateAppointmentNumber()
    {
        $prefix = 'APT';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return $prefix . $date . $random;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) &&
               $this->schedule->schedule_date->isFuture();
    }
}
