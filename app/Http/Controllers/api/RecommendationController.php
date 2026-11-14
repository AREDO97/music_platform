<?php

namespace App\Http\Controllers\api;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Track;
class RecommendationController extends Controller
{
    //
public function favouriteGenres(Request $request)
{
    $genres = DB::table('track_plays')
        ->join('tracks', 'track_plays.track_id', '=', 'tracks.id')
        ->join('genres', 'tracks.genre_id', '=', 'genres.id')
        ->where('track_plays.user_id', $request->user()->id)
        ->select(
            'genres.id',
            'genres.name',
            DB::raw('COUNT(track_plays.id) as total_plays')
        )
        ->groupBy('genres.id', 'genres.name')
        ->orderByDesc('total_plays')
        ->get();

    return response()->json([
        'message' => 'Favourite genres retrieved successfully',
        'data' => $genres
    ]);
}


public function forYou(Request $request)
{
    $userId = $request->user()->id;

    // 1. Find the user's top 3 favourite genres
    $genreIds = DB::table('track_plays')
        ->join('tracks', 'track_plays.track_id', '=', 'tracks.id')
        ->where('track_plays.user_id', $userId)
        ->whereNotNull('tracks.genre_id')
        ->select('tracks.genre_id')
        ->selectRaw('COUNT(track_plays.id) as total_plays')
        ->groupBy('tracks.genre_id')
        ->orderByDesc('total_plays')
        ->limit(3)
        ->pluck('tracks.genre_id');

    // 2. If the user has no listening history,
    // recommend globally popular tracks
    if ($genreIds->isEmpty()) {

        $tracks = Track::where('is_active', true)
            ->with('genre')
            ->withCount('trackPlays')
            ->orderByDesc('track_plays_count')
            ->take(10)
            ->get();

    } else {

        // 3. Recommend tracks from favourite genres
        $tracks = Track::where('is_active', true)
            ->whereIn('genre_id', $genreIds)
            ->where('user_id', '!=', $userId)
            ->with('genre')
            ->withCount('trackPlays')
            ->orderByDesc('track_plays_count')
            ->take(10)
            ->get();
    }

    return response()->json([
        'message' => 'Recommended tracks retrieved successfully',
        'data' => $tracks
    ]);
}
}
