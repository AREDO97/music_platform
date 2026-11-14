<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\WatchList;
use App\Models\Video;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    // Add video to watchlist
    public function create(Request $request, Video $video)
    {
        $user = $request->user();

        // FirstOrCreate 
        $watchList = WatchList::firstOrCreate([
            'user_id'  => $user->id,
            'video_id' => $video->id
        ]);

        return response()->json([
            'message'   => 'Video added to watchlist',
            'watchlist' => $watchList->load('video')
        ], 201);
    }

    // Remove video from watchlist
    public function destroy(Request $request, Video $video)
    {
        $user = $request->user();

        $watchList = WatchList::where('user_id', $user->id)
            ->where('video_id', $video->id)
            ->first();

        if (!$watchList) {
            return response()->json([
                'message' => 'Video not found in watchlist'
            ], 404);
        }

        $watchList->delete();

        return response()->json([
            'message' => 'Video removed from watchlist'
        ]);
    }

    // View user watchlist
    public function index(Request $request)
    {
        $user = $request->user();
        
        $watchlist = WatchList::where('user_id', $user->id)
            ->with('video.category', 'video.user')
            ->latest()
            ->get();

        return response()->json($watchlist);
    }
}