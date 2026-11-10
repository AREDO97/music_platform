<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VideoCategory;

class VideoCategoryController extends Controller
{
    //

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
        $category = VideoCategory::create($validated);
        // response
        return response()->json([
            'message' =>'VideoCategory created successifully',
            'VideoCategory'   =>$category
        ]);
    }
    // update VideoCategory
    public function update(Request $request,VideoCategory $category)
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
        $category->update([
            'name'=>$request->name ?? $category->name,
            'description' =>$request->description ?? $category->description
        ]);
        // response
        return response()->json($category);
    }
    // all VideoCategorys
    public function index(Request $request)
    {
        $VideoCategorys = VideoCategory::where('is_active',true)->latest()->get();
        // response
        return response()->json($VideoCategorys);
    }
    // delete VideoCategory
    public function destroy(Request $request,VideoCategory $category)
    {
           // check user role
    $user = $request->user();
    if($user->role !== 'admin' && $user->role !== 'super_admin')
        {
            abort(403,'Unauthorised action');
        }
        // delete VideoCategory
        $category->update([
            'is_active'=>false
        ]);
        // response
        return response()->json($category);
    }
}
