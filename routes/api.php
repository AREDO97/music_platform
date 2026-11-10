<?php

use App\Http\Controllers\api\AuditLogController;
use App\Http\Controllers\api\NotificationsController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\api\SettingsController;
use App\Http\Controllers\api\HealthController;
use App\Http\Controllers\api\ProfileController;
use Illuminate\Http\Request;

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


