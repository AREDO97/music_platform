<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
        use HasFactory;
    // allowed
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    // tracks
    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

}



