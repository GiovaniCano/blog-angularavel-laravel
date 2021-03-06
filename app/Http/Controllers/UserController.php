<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UserController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UpdatesUserProfileInformation $updater)
    {
        $email = $request->user()->email;
        
        $updater->update($request->user(), $request->all());

        return new JsonResponse([
            'mustVerifyEmail' => $request->user()->email !== $email,
            'user' => $request->user()
        ], 200);
    }

    function current() {
        return response()->json( auth()->user() );
    }

    function getUser($name) {
        $user = User::where('name', $name)->firstOrFail();
        unset($user->email);
        unset($user->email_verified_at);
        unset($user->created_at);
        unset($user->updated_at);
        return response()->json( $user->load(['posts' => function($query) {
            $query->orderBy('id', 'desc');
        }]) );
    }

    function getAvatar($avatar) {
        if(Storage::disk('avatars')->exists($avatar)) {
            return Storage::disk('avatars')->get($avatar);
        } else {
            return response(null, 404);
        }
    }

    function deleteAvatar(Request $request) {
        $user = $request->user();

        if($user->avatar) {
            Storage::disk('avatars')->delete( $user->avatar );
            $user->avatar = null;
            $user->update();
        }

        return response(null);
    }

    function destroy(Request $request) {
        $request->validate(['password' => 'required|current_password:web']);

        $user = $request->user();

        $images = Post::select('image')->where('user_id', $user->id)->get();
        foreach($images as $image) {
            Storage::disk('posts')->delete($image->image);
        }

        if($user->avatar) Storage::disk('avatars')->delete( $user->avatar );
        $user->delete();

        return response(null);
    }
}
