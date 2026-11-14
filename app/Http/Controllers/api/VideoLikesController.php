<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoLike;
use Illuminate\Http\Request;

class VideoLikesController extends Controller
{
    // like a video
    public function create(Request $request,Video $video)
    {
        $user = $request->user();
        $liked = VideoLike::where('user_id',$user->id)
        ->where('video_id',$video->id)->first();
        if (!$liked)
            {
                $like =VideoLike::create([
                    'user_id' =>$user->id,
                    'video_id'=>$video->id
                ]);
                // response
                return response()->json($like);
            }
            else
                {
                $liked->delete();
                // message
                return response()->json([
                    'message'=>'Video unliked'
                ]);
            }
    }
    // video likes
    public function index(Request $request,Video $video)
    {
        $likes=$video->likes;
        $count= $likes->count();
        return response()->json([
            'likes_count'=>$count,
            'likes'=>$likes->load('user.profile')
        ]);
    }
}
