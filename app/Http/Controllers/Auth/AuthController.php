<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'validation_errors' => $validator->errors()
            ]);
        }

        $credentials = $request->only('email', 'password');
        try {
            if(!$token = auth()->attempt($credentials)){
                throw new UnauthorizedHttpException('User name or password is not valid');
            }
        } catch (JWTException $e) {
            throw $e;
        }

        return $this->responseWithToken($token);
    }

    public function refresh()
    {
        try {
            if (!$token = auth('api')->getToken())
            {
                throw new NotFoundHttpException('Token does not exist');
            }

            return $this->responseWithToken(auth('api')->refresh($token));

        } catch (JWTException $e) {
            throw $e;
        }
    }

    public function logout()
    {
        try {
            auth('api')->logout();
        } catch (JWTException $e) {
            throw $e;
        }

        return response()->json([
            'status' => 200,
            'message' => 'user logged out successfully'
        ]);
    }
    public function me()
    {
        $me = User::where('id', auth('api')->user()->id)->with('roles')->first();
        return response()->json($me);
    }


    public function responseWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
