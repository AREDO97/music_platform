<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'date_of_birth',
        'gender',
        'country',
        'bio',
        'avatar',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Relationship: A profile belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}