<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\TrackLike;
use Illuminate\Http\Request;

class TrackLikeController extends Controller
{
    //

    // like an audio
    public function create(Request $request,Track $track)
    {
        $user = $request->user();
        $liked =TrackLike::where('user_id',$user->id)
        ->where('track_id',$track->id)->first();
        if (!$liked)
            {
                $like =TrackLike::create([
                    'user_id' =>$user->id,
                    'track_id'=>$track->id
                ]);
                // response
                return response()->json($like);
            }
            else
                {
                $liked->delete();
                // message
                return response()->json([
                    'message'=>'Track unliked'
                ]);
            }
    }

      // track likes
    public function index(Request $request,Track $track)
    {
        $likes=$track->trackLikes;
        $count= $likes->count();
        return response()->json([
            'likes_count'=>$count,
            'likes'=>$likes->load('user.profile')
        ]);
    }
}
