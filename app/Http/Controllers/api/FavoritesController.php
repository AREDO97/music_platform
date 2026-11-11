<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Track;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    // make favorites
    public function create(Request $request,Track $track)
    {
        $user = $request->user();
        // make favorite
        $favorite = Favorite::firstOrCreate([
            'user_id' => $user->id,
            'track_id'=>$track->id
        ]);
        // response
        return response()->json([
            'message'=>'Track added to favorites',
            'favorite'=>$favorite->load('track')
        ]);
    }
    // remove track from favorites
   
    // remove track from favorites
    public function destroy(Request $request, Track $track)
    {
        $user = $request->user();

        // Find the favorite entry belonging to this user and track
        $favorite = Favorite::where('user_id', $user->id)
            ->where('track_id', $track->id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'message' => 'Favorite not found'
            ], 404);
        }

        // Delete favorite
        $favorite->delete();

        // Response
        return response()->json([
            'message' => 'Track removed from favorites'
        ]);
    }
    // user favorites
    public function index(Request $request)
    {
             $user = $request->user();
            $favorites = $user->favorites()->with('track')->get();
            // response
            return response()->json($favorites);
    }
}
