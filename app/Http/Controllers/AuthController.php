<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\BaseController;

class AuthController extends BaseController
{
    public function login(Request $request){
        try {
         
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    if($user->role == 'ADMIN'){
                        $token = $user->createToken('MyApp', ['admin'])->accessToken;
                    } else {
                        $token = $user->createToken('MyApp', ['user'])->accessToken;
                    }
                    $response = ['token' => $token];
                    return $this->sendResponse($response, trans('response.login_success'));
                } else {
                    return $this->sendError('Password mismatch', 422);
                }
            } else {
                return $this->sendError('User does not exist', 422);
            }
        } catch (\Throwable $ex) {
            return $this->sendError($ex->getMessage(), $ex->getCode());
        }
    }
}
