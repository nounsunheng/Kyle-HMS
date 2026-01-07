<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relationships
     */

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Get the number of doctors in this specialty
     */
    public function getDoctorCountAttribute()
    {
        return $this->doctors()->count();
    }
}
