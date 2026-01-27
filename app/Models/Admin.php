<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Admin extends Model
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
        'profile_image',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessors
     */
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * Get profile image URL
     */
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            // Check if file exists
            if (Storage::disk('public')->exists($this->profile_image)) {
                return asset('storage/' . $this->profile_image);
            }
        }

        // Default avatar with admin color
        $initial = strtoupper(substr($this->user->name, 0, 1));
        return "https://ui-avatars.com/api/?name={$initial}&size=500&background=EF4444&color=fff&bold=true";
    }
}
