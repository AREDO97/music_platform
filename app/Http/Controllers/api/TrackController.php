<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use getID3;
use App\Models\Track;
use App\Models\User;
use App\Notifications\trackCreationNotification;

class TrackController extends Controller
{
    // upload track
public function create(Request $request)
{
    $user=$request->user();
    $request->validate([
        'title' => 'required|string',
        'audio' => 'required|file|mimes:mp3,wav,aac,m4a|max:20480', // max 20MB
        'genre_id' => 'required|exists:genres,id',
        'image_cover' => 'required|image',
        'description' =>'nullable|max:100'
    ]);

    //  Store the uploaded audio file
    $audioPath = $request->file('audio')->store('tracks/audio', 'public');
    $coverPath = $request->file('image_cover')->store('tracks/covers', 'public');

    //  Extract duration in seconds using getID3
    $fullAudioPath = storage_path('app/public/' . $audioPath);
    $getID3 = new getID3();
    $fileInfo = $getID3->analyze($fullAudioPath);
    
    // Fallback to 0 if play-time cannot be read
    $durationSeconds = isset($fileInfo['playtime_seconds']) ? (int) round($fileInfo['playtime_seconds']) : 0;

    // 3. Create the track record
    $track = Track::create([
        'user_id'          => $request->user()->id,
        'genre_id'         => $request->genre_id,
        'title'            => $request->title,
        'description'      => $request->description,
        'audio_path'       => $audioPath,
        'image_cover'      => $coverPath,
        'duration_seconds' => $durationSeconds,
        'is_active'        => true,
    ]);

    // notify admins
    $users= User::whereIn('role',['admin','super_admin'])->get();
    foreach($users as $admin)
        {
            $admin->notify(
                new trackCreationNotification($user->name)
            );
        }
    return response()->json([
        'message' => 'Track uploaded successfully',
        'track'   => $track,
    ], 201);
}

    // update track info 
// update track info 
    public function update(Request $request, Track $track)
    {
        $request->validate([
            'title'       => 'nullable|string',
            'audio'       => 'nullable|file|mimes:mp3,wav,aac,m4a|max:20480', // max 20MB
            'image_cover' => 'nullable|image',
            'description' => 'nullable|max:100', 
        ]);

        $audioPath = $track->audio_path;
        $coverPath = $track->image_cover;
        $durationSeconds = $track->duration_seconds;

        // Check for new audio file (Fixed typo: audiio -> audio)
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('tracks/audio', 'public');

            // Extract new duration only if a new audio file is uploaded
            $fullAudioPath = storage_path('app/public/' . $audioPath);
            $getID3 = new \getID3();
            $fileInfo = $getID3->analyze($fullAudioPath);
            
            $durationSeconds = isset($fileInfo['playtime_seconds']) 
                ? (int) round($fileInfo['playtime_seconds']) 
                : $track->duration_seconds;
        }

        // Check for new cover image
        if ($request->hasFile('image_cover')) {
            $coverPath = $request->file('image_cover')->store('tracks/covers', 'public');
        }

        // Update the track record
        $track->update([
            'title'            => $request->input('title', $track->title),
            'description'      => $request->input('description', $track->description),
            'audio_path'       => $audioPath,
            'image_cover'      => $coverPath,
            'duration_seconds' => $durationSeconds,
        ]);

        return response()->json([
            'message' => 'Track updated successfully',
            'track'   => $track->fresh(),
        ], 200); // Status 200 for updates instead of 201
    }

    // view audios
    public function index(Request $request)
    {
        $user = $request->user();
        // tracks
        $tracks = Track::where('is_active',true)->latest()->paginate(12);
        // responnse
        return response()->json($tracks);
    }
    // delete song
    public function destroy(Request $request,Track $track)
    {
         $user = $request->user();
         if($user->id !== $track->user_id)
            {
                abort(403,'Unauthorised action');
            }
            // soft delete track
            $track->update([
                'is_active' =>false
            ]);
            // response
            return response()->json([
                'message' =>'Track deleted succesifully',
                'track'   =>$track
            ]);
    }
}
