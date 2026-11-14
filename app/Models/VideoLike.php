<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoLike extends Model
{
    // allowed
    protected $fillable = [
        'user_id',
        'video_id'
    ];
    // video
    public function video()
    {
        return $this->belongsTo(Video::class);
    }
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
