<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoView;
use Illuminate\Http\Request;

class VideoViewController extends Controller
{
    // create view
    public function create(Request $request,Video $video)
    {
        $user = $request->user();
        // insert view
        $view = VideoView::create([
            'user_id'=>$user->id,
            'video_id'=>$video->id
        ]);
        // response
        return response()->json($view);
    }
    // video views
    public function index(Request $request,Video $video)
    {
        $views = $video->videoViews;
        $totalViews= $views->count();
        // response
        return response()->json(
            [
                'total_views'=>$totalViews,
                'Views'=>$views->load('video')

            ]
        );
    }
    // video views summary
    public function summary(Request $request)
    {
            // check user role
    $user = $request->user();
    if($user->role !== 'admin' && $user->role !== 'super_admin')
        {
            abort(403,'Unauthorised action');
        }
        // total views
        $totalVideoViews=VideoView::all()->count();
        // video with highest views
        $videos = Video::withCount('videoViews')
    ->orderByDesc('video_views_count')
    ->take(10)
    ->get();
    // response
    return response()->json([
        'totalViews'=>$totalVideoViews,
        'highestViews'=>$videos
    ]);
    }
}
