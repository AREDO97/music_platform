<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    // create genre
    public function create(Request $request)
    {
        // check user role
    $user = $request->user();
    if($user->role !== 'admin' && $user->role !== 'super_admin')
        {
            abort(403,'Unauthorised action');
        }
        // vaidate input
        $validated = $request->validate([
            'name' =>'required|string',
            'description'=>'nullable|max:100'
        ]);
        // create genre
        $genre = Genre::create($validated);
        // response
        return response()->json([
            'message' =>'Genre created successifully',
            'genre'   =>$genre
        ]);
    }
    // update genre
    public function update(Request $request,Genre $genre)
    {
            // check user role
    $user = $request->user();
    if($user->role !== 'admin' && $user->role !== 'super_admin')
        {
            abort(403,'Unauthorised action');
        }
        // vaidate input
        $validated = $request->validate([
            'name' =>'nullable|string',
            'description'=>'nullable|max:100'
        ]);
        // update
        $genre->update([
            'name'=>$request->name ?? $genre->name,
            'description' =>$request->description ?? $genre->description
        ]);
        // response
        return response()->json($genre);
    }
    // all genres
    public function index(Request $request)
    {
        $genres = Genre::where('is_active',true)->latest()->get();
        // response
        return response()->json($genres);
    }
    // delete genre
    public function destroy(Request $request,Genre $genre)
    {
           // check user role
    $user = $request->user();
    if($user->role !== 'admin' && $user->role !== 'super_admin')
        {
            abort(403,'Unauthorised action');
        }
        // delete genre
        $genre->update([
            'is_active'=>false
        ]);
        // response
        return response()->json($genre);
    }
        
}
