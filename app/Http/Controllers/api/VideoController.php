<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use getID3;
use App\Models\Video;
use App\Models\VideoCategory;
use App\Notifications\videoCreationNotification;

class VideoController extends Controller
{
    // Upload a new video
    public function create(Request $request)
    {
        $user=$request->user();
        $request->validate([
            'title'             => 'required|string',
            'video'             => 'required|file|mimes:mp4,mov,avi,mkv,webm|max:102400', // max 100MB for video
            'video_category_id' => 'required|exists:video_categories,id',
            'image_cover'       => 'required|image',
            'description'       => 'nullable|max:100',
        ]);

        // Store the uploaded video and cover files
        $videoPath = $request->file('video')->store('videos/files', 'public');
        $coverPath = $request->file('image_cover')->store('videos/covers', 'public');

        // Extract duration in seconds using getID3
        $fullVideoPath = storage_path('app/public/' . $videoPath);
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($fullVideoPath);
        
        $durationSeconds = isset($fileInfo['playtime_seconds']) ? (int) round($fileInfo['playtime_seconds']) : 0;

        // Create the video record
        $video = Video::create([
            'user_id'           => $request->user()->id,
            'video_category_id' => $request->video_category_id,
            'title'             => $request->title,
            'description'       => $request->description,
            'video_path'        => $videoPath,
            'image_cover'       => $coverPath,
            'duration_seconds'  => $durationSeconds,
            'is_active'         => true,
        ]);

        // notify admins
    $users= User::whereIn('role',['admin','super_admin'])->get();
    foreach($users as $admin)
        {
            $admin->notify(
                new videoCreationNotification($user->name)
            );
        }
        return response()->json([
            'message' => 'Video uploaded successfully',
            'video'   => $video,
        ], 201);
    }

    // Update video info
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title'             => 'nullable|string',
            'video'             => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:102400',
            'image_cover'       => 'nullable|image',
            'description'       => 'nullable|max:100',
            'video_category_id' => 'nullable|exists:video_categories,id',
        ]);

        $videoPath = $video->video_path;
        $coverPath = $video->image_cover;
        $durationSeconds = $video->duration_seconds;

        // Check for new video file
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos/files', 'public');

            $fullVideoPath = storage_path('app/public/' . $videoPath);
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($fullVideoPath);
            
            $durationSeconds = isset($fileInfo['playtime_seconds']) 
                ? (int) round($fileInfo['playtime_seconds']) 
                : $video->duration_seconds;
        }

        // Check for new cover image
        if ($request->hasFile('image_cover')) {
            $coverPath = $request->file('image_cover')->store('videos/covers', 'public');
        }

        // Update the video record
        $video->update([
            'title'             => $request->filled('title') ? $request->title : $video->title,
            'description'       => $request->has('description') ? $request->description : $video->description,
            'video_category_id' => $request->filled('video_category_id') ? $request->video_category_id : $video->video_category_id,
            'video_path'        => $videoPath,
            'image_cover'       => $coverPath,
            'duration_seconds'  => $durationSeconds,
        ]);

        return response()->json([
            'message' => 'Video updated successfully',
            'video'   => $video->fresh(),
        ], 200);
    }

    // View active videos (paginated)
    public function index(Request $request)
    {
        $videos = Video::with(['user', 'category'])
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return response()->json($videos);
    }

    // Soft delete video
    public function destroy(Request $request, Video $video)
    {
        $user = $request->user();
        
        if ($user->id !== $video->user_id && $user->role !== 'admin' && $user->role !== 'super_admin') {
            abort(403, 'Unauthorised action');
        }

        $video->update([
            'is_active' => false
        ]);

        return response()->json([
            'message' => 'Video deleted successfully',
            'video'   => $video
        ]);
    }
}