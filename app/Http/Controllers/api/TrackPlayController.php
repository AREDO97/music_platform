<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\TrackPlay;
use Illuminate\Http\Request;

class TrackPlayController extends Controller
{
    // create play record
    public function create(Request $request, Track $track)
    {
        $user = $request->user();
        // insert play
        $play = TrackPlay::create([
            'user_id' => $user->id,
            'track_id' => $track->id
        ]);

        // response
        return response()->json($play, 201);
    }

    // track plays index
    public function index(Request $request, Track $track)
    {
        $plays = $track->trackPlays;
        $totalPlays = $plays->count();

        // response
        return response()->json([
            'total_plays' => $totalPlays,
            'plays' => $plays->load('track')
        ]);
    }

    // track plays summary (Admin only)
    public function summary(Request $request)
    {
        // check user role
        $user = $request->user();
        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            abort(403, 'Unauthorised action');
        }

        // total plays across platform
        $totalTrackPlays = TrackPlay::all()->count();

        // top 10 tracks with highest plays
        $tracks = Track::withCount('trackPlays')
            ->orderByDesc('track_plays_count')
            ->take(10)
            ->get();

        // response
        return response()->json([
            'totalPlays' => $totalTrackPlays,
            'highestPlays' => $tracks
        ]);
    }
}