<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoComment;
use Illuminate\Http\Request;

class VideoCommentsController extends Controller
{
    // create comment
    public function create(Request $request,Video $video)
    {
        $user = $request->user();
        $request->validate([
            'text'=>'required|max:100'
        ]);
        // comment
        $comment = VideoComment::create([
            'user_id'  =>$user->id,
            'video_id' =>$video->id,
            'text'     =>$request->text
        ]);
        // response
        return response()->json($comment);
    }
    // video comments
    public function index(Request $request,Video $video)
    {
         $user = $request->user();  
         $comments = $video->videoComments;
         // response
         return response()->json($comments->load('user.profile'));
    }
    // edit comment
    public function update(Request $request,VideoComment $comment)
    {
        $user = $request->user(); 
        // validate
        $request->validate([
            'text'=>'required|max:100'
        ]);
        if($user->id !== $comment->user_id)
            {
                abort(403,'Unauthorised action');
            }
            // update comment
            $comment->update([
                'text'=>$request->text
            ]);
            // response
            return response()->json($comment->fresh());
    }
    // delete comment
    public function destroy(Request $request,VideoComment $comment)
    {
        
        $user = $request->user(); 
        
        if($user->id !== $comment->user_id)
            {
                abort(403,'Unauthorised action');
            } 
            // delete comment
            $comment->delete();
            // response
            return response()->json([
                'message'=>'Comment deleted successifully'
            ]);
    }
}
