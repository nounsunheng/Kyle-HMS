<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doctor_id',
        'schedule_date',
        'start_time',
        'end_time',
        'duration_per_appointment',
        'max_appointments',
        'booked_appointments',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'schedule_date' => 'date',
        'duration_per_appointment' => 'integer',
        'max_appointments' => 'integer',
        'booked_appointments' => 'integer',
    ];

    /**
     * Relationships
     */

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Accessors
     */

    public function getAvailableSlotsAttribute()
    {
        return $this->max_appointments - $this->booked_appointments;
    }

    public function getIsFullAttribute()
    {
        return $this->booked_appointments >= $this->max_appointments;
    }

    public function getFormattedDateAttribute()
    {
        return $this->schedule_date->format('F d, Y');
    }

    public function getFormattedTimeRangeAttribute()
    {
        return Carbon::parse($this->start_time)->format('g:i A') . ' - ' .
               Carbon::parse($this->end_time)->format('g:i A');
    }

    /**
     * Scopes
     */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('schedule_date', '>=', now()->toDateString())
                    ->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
                    ->whereColumn('booked_appointments', '<', 'max_appointments');
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Helper methods
     */

    public function incrementBookedAppointments()
    {
        $this->increment('booked_appointments');
    }

    public function decrementBookedAppointments()
    {
        if ($this->booked_appointments > 0) {
            $this->decrement('booked_appointments');
        }
    }
}
