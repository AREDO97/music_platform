<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'genre_id',
        'title',
        'description',
        'audio_path',
        'image_cover',
        'duration_seconds',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_seconds' => 'integer',
    ];

    /**
     * Relationship: A track belongs to a user .
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A track belongs to a genre.
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * Optional Accessor: Format duration_seconds into MM:SS (e.g., 3:45).
     */
    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
    // favorites
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    // ratings
     public function ratings()
    {
        return $this->hasMany(AudioRating::class);
    }
}