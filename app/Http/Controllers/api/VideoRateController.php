<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\VideoRating;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoRateController extends Controller
{
    // Rate a video
    public function create(Request $request, Video $video)
    {
        $user = $request->user();
        
        // Validation
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        // Create or update video rating
        $rating = VideoRating::updateOrCreate(
            [
                'user_id'  => $user->id,
                'video_id' => $video->id
            ],
            [
                'rating'   => $request->rating
            ]
        );

        return response()->json([
            'message' => 'Video rated successfully',
            'rating'  => $rating->fresh()
        ]);
    }

    // Return video ratings and average
    public function index(Request $request, Video $video)
    {
        $user = $request->user();
        
        $totalSum = $video->videoRatings()->sum('rating');
        $totalUsers = $video->videoRatings()->count();

        // Prevent division by zero safely
        $averageRating = $totalUsers > 0 ? round($totalSum / $totalUsers, 2) : 0;

        return response()->json([
            'total_rating'   => $totalSum,
            'total_user'     => $totalUsers,
            'average_rating' => $averageRating,
        ]);
    }
}