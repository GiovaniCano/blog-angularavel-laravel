<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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
}
