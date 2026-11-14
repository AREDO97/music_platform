<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    //
    use HasFactory;

        protected $fillable = [
        'user_id',
        'video_category_id',
        'title',
        'description',
        'video_path',
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

public function category()
{
    return $this->belongsTo(VideoCategory::class, 'video_category_id');
}

    /**
     * Format duration_seconds into MM:SS 
     */
    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

     // videoratings
    public function videoRatings()
    {
        return $this->hasMany(VideoRating::class);
    }

     // videoComments
    public function videoComments()
    {
        return $this->hasMany(VideoComment::class);
    }

    // likes
    public function likes()
    {
        return $this->hasMany(VideoLike::class);
    }
    // videoViews
    public function videoViews()
    {
        return $this->hasMany(VideoView::class);
    }
}

