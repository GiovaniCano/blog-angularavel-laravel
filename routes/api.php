<?php

use App\Http\Controllers\AuthController;
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
/* 404 */
// Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) { return $request->user(); });
Route::get('email/verify/{id}/{hash}', function() { return response('Page Not Found', 404); });
Route::fallback( function(){ return response('Page Not Found', 404); } );
