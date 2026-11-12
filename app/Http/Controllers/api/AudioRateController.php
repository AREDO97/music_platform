<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AudioRating;
use App\Models\Track;
use Illuminate\Http\Request;

class AudioRateController extends Controller
{
    // rate
    public function create(Request $request,Track $track)
    {
        $user = $request->user();
        // validation
        $request->validate([
            'rating'=>'sometimes|integer'
        ]);
        // create rating
        $rating =AudioRating::updateOrCreate(
            [
                'user_id'=>$user->id,
                'track_id' =>$track->id
            ],
            [
                'rating'=>$request->rating
            ]
        );
        // response
        return response()->json($rating->fresh());
    }
    // return song ratings
  // return song ratings
public function index(Request $request, Track $track)
{
    $user = $request->user();
    $rating = $track->ratings()->sum('rating');
    $total_users = $track->ratings()->count();

    // Prevent division by zero safely
    $average_rating = $total_users > 0 ? round($rating / $total_users, 2) : 0;

    return response()->json([
        'total_rating'   => $rating,
        'total_user'     => $total_users,
        'average_rating' => $average_rating,
    ]);
}
}
