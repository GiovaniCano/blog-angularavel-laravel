<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function emailverify(Request $request) {
        if($request->user()->email_verified_at) {
            return response()->json( ['message' => 'Email already verified'] );
        }

        $request->validate(['token'=>'required|string']);

        $token_lifetime = 900; // 15 min

        $response = [ ['message'=>'Invalid token'], 422 ]; //default error
        try {
            $payload = explode('.', Crypt::decryptString($request->token));
            $token_user_id = intval($payload[0]);
            $creation_time = $payload[1];
            
            $user = $request->user();
            if($token_user_id === $user->id && time() - $creation_time < $token_lifetime) {
                $user->email_verified_at = time();
                $user->update();
                $response = [[], 204];
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json( $response[0], $response[1] );
    }

    function isEmailAvailable(Request $request) {
        $rules = $request->user() ? ['email' => 'required|unique:users,email,'.$request->user()->id] : ['email' => 'required|unique:users,email'];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) return response()->json(false, 200);
        return response()->json(true, 200);
    }
    function isUsernameAvailable(Request $request) {
        $rules = $request->user() ? ['name' => 'required|unique:users,name,'.$request->user()->id] : ['name' => 'required|unique:users,name'];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) return response()->json(false, 200);
        return response()->json(true, 200);
    }
}
