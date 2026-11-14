<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackPlay extends Model
{
    // allowed
    protected $fillable = [
        'user_id',
        'track_id'
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