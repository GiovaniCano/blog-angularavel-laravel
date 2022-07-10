<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    function current() {
        return response()->json( auth()->user() );
    }

    function getUser($id) {
        $user = User::findOrFail($id);
        unset($user->email);
        unset($user->email_verified_at);
        unset($user->created_at);
        unset($user->updated_at);
        return response()->json( $user->load('posts') );
    }

    function getAvatar($avatar) {
        if(Storage::disk('avatars')->exists($avatar)) {
            return Storage::disk('avatars')->get($avatar);
        } else {
            return response(null, 404);
        }
    }

    function destroy(Request $request) {
        $request->validate(['password' => 'required|current_password:web']);

        $request->user()->delete();

        return response(null);
    }
}
