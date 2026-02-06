<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Appointment Model
 *
 * Represents a medical appointment between a patient and doctor.
 * Handles appointment lifecycle from creation to completion/cancellation.
 *
 * @property int $id
 * @property int $patient_id
 * @property int $schedule_id
 * @property string $appointment_number Unique appointment identifier
 * @property string $appointment_time Time of appointment
 * @property string $status Current appointment status
 * @property string|null $reason Patient's reason for visit
 * @property string|null $notes Additional notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Appointment extends Model
{
    use HasFactory;

    /**
     * Appointment status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';
    const STATUS_EXPIRED = 'expired';

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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the patient that owns the appointment
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the schedule that the appointment belongs to
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the medical record associated with the appointment
     */
    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    /**
     * Get the doctor through the schedule relationship
     */
    public function doctor()
    {
        return $this->hasOneThrough(
            Doctor::class,
            Schedule::class,
            'id',              // Foreign key on schedules table
            'id',              // Foreign key on doctors table
            'schedule_id',     // Local key on appointments table
            'doctor_id'        // Local key on schedules table
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (Getters)
    |--------------------------------------------------------------------------
    */

    /**
     * Get formatted time (e.g., "9:30 AM")
     */
    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->appointment_time)->format('g:i A');
    }

    /**
     * Get full date and time string
     */
    public function getFullDateTimeAttribute(): string
    {
        return $this->schedule->schedule_date->format('F d, Y') . ' at ' . $this->formatted_time;
    }

    /**
     * Get Tailwind CSS classes for status badge
     *
     * Returns complete Tailwind utility classes for displaying
     * appointment status with appropriate colors.
     *
     * @return string CSS classes for badge styling
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING    => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
            self::STATUS_CONFIRMED  => 'bg-blue-100 text-blue-800 border border-blue-300',
            self::STATUS_COMPLETED  => 'bg-green-100 text-green-800 border border-green-300',
            self::STATUS_CANCELLED  => 'bg-red-100 text-red-800 border border-red-300',
            self::STATUS_NO_SHOW    => 'bg-gray-900 text-white border border-gray-900',
            self::STATUS_EXPIRED    => 'bg-orange-100 text-orange-800 border border-orange-300',
            default                 => 'bg-gray-200 text-gray-700 border border-gray-400',
        };
    }

    /**
     * Get color name for the current status
     * Useful for UI components
     *
     * @return string Color name
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING    => 'yellow',
            self::STATUS_CONFIRMED  => 'blue',
            self::STATUS_COMPLETED  => 'green',
            self::STATUS_CANCELLED  => 'red',
            self::STATUS_NO_SHOW    => 'gray',
            self::STATUS_EXPIRED    => 'orange',
            default                 => 'gray',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope query to only pending appointments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope query to only confirmed appointments
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope query to only completed appointments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope query to only cancelled appointments
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope query to only expired appointments
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope query to upcoming appointments (future & active statuses)
     */
    public function scopeUpcoming($query)
    {
        return $query->whereHas('schedule', function ($q) {
            $q->where('schedule_date', '>=', now()->toDateString());
        })->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Scope query to past appointments
     */
    public function scopePast($query)
    {
        return $query->whereHas('schedule', function ($q) {
            $q->where('schedule_date', '<', now()->toDateString());
        })->orWhereIn('status', [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_NO_SHOW,
            self::STATUS_EXPIRED
        ]);
    }

    /**
     * Scope query to specific patient's appointments
     */
    public function scopeForPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Generate unique appointment number
     *
     * Format: APT + YYYYMMDD + 4 random uppercase letters
     * Example: APT20260201XYZW
     *
     * @return string Unique appointment number
     */
    public static function generateAppointmentNumber(): string
    {
        $prefix = 'APT';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return $prefix . $date . $random;
    }

    /**
     * Check if appointment can be cancelled
     *
     * @return bool True if cancellable, false otherwise
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]) &&
               $this->schedule->schedule_date->isFuture();
    }

    /**
     * Check if appointment can be confirmed
     *
     * @return bool True if confirmable, false otherwise
     */
    public function canBeConfirmed(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if appointment can be completed
     *
     * @return bool True if completable, false otherwise
     */
    public function canBeCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }
}
