<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    // View authenticated user's profile
    public function show(Request $request)
    {
        $profile = $request->user()->profile()->with('user')->first();

        if (!$profile) {
            return response()->json([
                'message' => 'Profile not found. Please create one.'
            ], 404);
        }

        return response()->json($profile);
    }
   

    // Update user profile (supports POST with multipart/form-data)
    public function update(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json([
                'message' => 'Profile not found.'
            ], 404);
        }

        $validated = $request->validate([
            'phone'         => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|string|in:male,female,other',
            'country'       => 'nullable|string|max:100',
            'bio'           => 'nullable|string|max:500',
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $avatarPath = $profile->avatar;
        if ($request->hasFile('avatar')) {
            // Optional: delete old avatar if exists
            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $avatarPath = $request->file('avatar')->store('profiles/avatars', 'public');
        }

        $profile->update([
            'phone'         => $request->filled('phone') ? $validated['phone'] : $profile->phone,
            'date_of_birth' => $request->has('date_of_birth') ? $validated['date_of_birth'] : $profile->date_of_birth,
            'gender'        => $request->filled('gender') ? $validated['gender'] : $profile->gender,
            'country'       => $request->filled('country') ? $validated['country'] : $profile->country,
            'bio'           => $request->has('bio') ? $validated['bio'] : $profile->bio,
            'avatar'        => $avatarPath,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile->fresh()
        ], 200);
    }

   
}