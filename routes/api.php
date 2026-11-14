<?php

use App\Http\Controllers\api\AudioRateController;
use App\Http\Controllers\api\AuditLogController;
use App\Http\Controllers\api\FavoritesController;
use App\Http\Controllers\api\GenreController;
use App\Http\Controllers\api\NotificationsController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\api\SettingsController;
use App\Http\Controllers\api\HealthController;
use App\Http\Controllers\api\ProfileController;
use App\Http\Controllers\api\TrackController;
use App\Http\Controllers\api\VideoController;
use App\Http\Controllers\api\WatchlistController;
use App\Http\Controllers\api\VideoCategoryController;
use App\Http\Controllers\api\VideoRateController;
use App\Http\Controllers\api\UserProfileController;
use App\Http\Controllers\api\VideoCommentsController;
use App\Http\Controllers\api\VideoLikesController;
use App\Http\Controllers\api\VideoViewController;
use Illuminate\Http\Request;
use App\Http\Controllers\api\TrackPlayController;
use Illuminate\Support\Facades\Route;

// register & login
Route::prefix('v1')->middleware(['throttle:10,1'])->group(function (){
// register user
Route::post('register',[AuthController::class,'register']);
// login user
Route::post('login',[authController::class,'login']);
});

// log out endpoint
Route::post('v1/logout',[authController::class,'logout'])->middleware('auth:sanctum')
->name('logout');

// google oauth
Route::get('v1/oauth/google', [AuthController::class, 'google'])
->name('google redirect');
Route::get('v1/oauth/google/callback', [AuthController::class, 'googleCallback'])
->name('google callback');


// current looged in user
Route::get('v1/getMe',[AuthController::class,'getMe'])->middleware('auth:sanctum')
->name('auth me');

// user management only admins $ super_admins
Route::prefix('v1')->middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
    // all users
    Route::get('users',[UserController::class,'index'])->name('view users');
    // user activity
    Route::get('userActivity',[UserController::class,'userActivity'])->name('user activity');
    // access a single user
    Route::get('users/{user}',[UserController::class,'oneUser'])->name('view single user');
    // update user info
    Route::patch('users/update/{user}',[UserController::class,'update'])->name('update user info');
    // suspend user
    Route::patch('users/suspend/{user}', [UserController::class, 'softDelete'])->name('suspend user');
    // create an admin
    Route::patch('users/create_admin/{user}', [UserController::class, 'makeAdmin'])->name('make admin');
    // demote admin to user
    Route::patch('users/demote_admin/{user}', [UserController::class, 'demoteAdmin'])->name('demote admin');
    // unsuspend user
     Route::patch('users/unsuspend/{user}', [UserController::class, 'unsuspend'])->name('unsuspend user');
    
});


   // notifications management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // all user notifications
  Route::get('notifications', [NotificationsController::class,'index'])
  ->name('all notifications');
   // mark notifications as read
  Route::post('notifications/markAsRead', [ NotificationsController::class, 'markAllAsRead'])
  ->name('marks as read');
   // delete notifications
  Route::delete('notifications/{id}',[NotificationsController::class,'destroy'])
  ->name('delete notification');
});

    // settings management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // updatePassword
Route::patch('password',[SettingsController::class,'updatePassword'])
->name('update password');
    // update username and email  updateUserInfo
Route::patch('usernameOrEmail',[SettingsController::class,'updateUserInfo'])
->name('update name or email');
    // delete account
Route::delete('deleteAccount',[SettingsController::class,'deleteAccount'])
->name('delete account');

});

// audit log management
Route::prefix('v1')->middleware(['auth:sanctum','role:super_admin,admin'])->group(function (){
    // view logs
    Route::get('logs',[AuditLogController::class,'index']);
    // delete log
    Route::delete('logs/{log}',[AuditLogController::class,'destroy']);
    // delete old logs
    Route::delete('logs',[AuditLogController::class,'clearAll']);
    // archive log
    Route::patch('logs/{log}/archive',[AuditLogController::class,'archive']);
});

// health checks
Route::prefix('v1')->middleware(['auth:sanctum','role:admin,super_admin','verified','throttle:10,1'])
->group(function () {
    // Check Laravel + MySQL 
    Route::get('health', [HealthController::class, 'health']); 
    // Checks if api is alive 
    Route::get('health/live', [HealthController::class, 'live']);
});


// get genres
    Route::get('v1/genres',[GenreController::class,'index'])
    ->middleware(['auth:sanctum']);
// genre management
Route::prefix('v1')->middleware(['auth:sanctum','role:admin,super_admin'])->group(function (){
    // create genre
    Route::post('genres',[GenreController::class,'create']);
    // update genre
    Route::patch('genres/{genre}',[GenreController::class,'update']);
    // delete genre
    Route::delete('genres/{genre}',[GenreController::class,'destroy']);
});


// track managent
 Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // get all tracks
    Route::get('tracks',[TrackController::class,'index']);
    // create track
    Route::post('tracks',[TrackController::class,'create']);
    // update track
    Route::post('tracks/{track}',[TrackController::class,'update']);
    // delete track
    Route::delete('tracks/{track}',[TrackController::class,'destroy']);
    });

// video category management
// get genres
    Route::get('v1/video_categories',[GenreController::class,'index'])
    ->middleware(['auth:sanctum']);
// genre management
Route::prefix('v1')->middleware(['auth:sanctum','role:admin,super_admin'])->group(function (){
    // create genre
    Route::post('video_categories',[VideoCategoryController::class,'create']);
    // update genre
    Route::patch('video_categories/{category}',[VideoCategoryController::class,'update']);
    // delete genre
    Route::delete('video_categories/{category}',[VideoCategoryController::class,'destroy']);
});

// video management
// track managent
 Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // get all tracks
    Route::get('videos',[VideoController::class,'index']);
    // create track
    Route::post('videos',[VideoController::class,'create']);
    // update track
    Route::post('videos/{video}',[VideoController::class,'update']);
    // delete track
    Route::delete('videos/{video}',[VideoController::class,'destroy']);
    });

// favorite management
 Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // user favorites
    Route::get('favorites',[FavoritesController::class,'index']);
    // make favorite
    Route::post('favorites/{track}',[FavoritesController::class,'create']);
    // remove from favorites
    Route::delete('favorites/{track}',[FavoritesController::class,'destroy']);
 });


 // Watchlist management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Get user watchlist
    Route::get('watchlist', [WatchlistController::class, 'index']);
    // Add video to watchlist
    Route::post('watchlist/{video}', [WatchlistController::class, 'create']);
    // Remove video from watchlist
    Route::delete('watchlist/{video}', [WatchlistController::class, 'destroy']);
});



Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // User Profile Management
    Route::get('profile', [UserProfileController::class, 'show']);
    Route::post('profile', [UserProfileController::class, 'store']);
    Route::post('profile/update', [UserProfileController::class, 'update']); 
    Route::delete('profile', [UserProfileController::class, 'destroy']);
});

// audio rating management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function (){
// create rating
Route::post('audio_ratings/{track}',[AudioRateController::class,'create']);
// show track rating
Route::get('audio_ratings/{track}',[AudioRateController::class,'index']);

});


// Video rating management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Rate a video
    Route::post('video_ratings/{video}', [VideoRateController::class, 'create']);
    // Show video ratings summary
    Route::get('video_ratings/{video}', [VideoRateController::class, 'index']);
});

// video comments
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
// video comments
Route::get('video_comments/{video}',[VideoCommentsController::class,'index']);
// comment on a video
Route::post('video_comments/{video}',[VideoCommentsController::class,'create']);
// update comment
Route::patch('video_comments/{comment}',[VideoCommentsController::class,'update']);
// delete comment
Route::delete('video_comments/{comment}',[VideoCommentsController::class,'destroy']);
});

// video likes management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
// like video
Route::post('video_likes/{video}',[VideoLikesController::class,'create']);
// get video likes
Route::get('video_likes/{video}',[VideoLikesController::class,'index']);
});

// video views management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
// summary of views
Route::get('video_views',[VideoViewController::class,'summary']);
// create view
Route::post('video_views/{video}',[VideoViewController::class,'create']);
// get views
Route::get('video_views/{video}',[VideoViewController::class,'index']);

});


// Track plays management
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Summary of plays (Admin)
    Route::get('track_plays', [TrackPlayController::class, 'summary']);
    // Record a play for a track
    Route::post('track_plays/{track}', [TrackPlayController::class, 'create']);
    // Get plays for a specific track
    Route::get('track_plays/{track}', [TrackPlayController::class, 'index']);
});