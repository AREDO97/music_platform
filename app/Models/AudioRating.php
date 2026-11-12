<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioRating extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'user_id',
        'track_id',
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
