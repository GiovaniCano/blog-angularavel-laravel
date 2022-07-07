<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    function current() {
        return auth()->user();
    }

    function getUser($id) {
        $user = User::findOrFail($id);
        unset($user->email);
        unset($user->email_verified_at);
        unset($user->created_at);
        unset($user->updated_at);
        return $user;
    }

    function getAvatar($avatar) {
        return Storage::disk('avatars')->get($avatar);
    }
}
