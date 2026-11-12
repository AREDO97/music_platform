<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoRating extends Model
{
    use HasFactory;
    //
     protected $fillable = [
        'user_id',
        'video_id',
        'rating'
    ];
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // track
    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}
