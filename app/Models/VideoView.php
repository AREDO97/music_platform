<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{
    // allowed
    protected $fillable = [
        'user_id',
        'video_id'
    ];
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // video
    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
