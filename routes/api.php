<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('email/verify', [AuthController::class, 'emailverify'])->middleware('auth:sanctum');

Route::controller(UserController::class)->group(function(){
    Route::get('user/current', 'current')->middleware(['auth:sanctum', 'verified']);
    Route::get('user/{id}', 'getUser');
    Route::get('user/avatar/{avatar}', 'getAvatar');
});

/* tests */
// Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) { return $request->user(); });

/* 404 and disabled Fortify routes */
Route::get('/reset-password/{token}', function(){
    return response('Page Not Found', 404);
})->middleware(['guest:'.config('fortify.guard')])->name('password.reset');

Route::get('email/verify/{id}/{hash}', function() { return response('Page Not Found', 404); });

Route::fallback( function(){ return response('Page Not Found', 404); } );
