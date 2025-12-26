<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ApiFormatter;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails())
                return response()->json(ApiFormatter::createJson(
                    400, 'Bad Request', $validator->errors()->all()
                ), 400);

            $user = User::where('email', $request->email)->first();
            if (!$user)
                return response()->json(ApiFormatter::createJson(
                    404, 'Account not found'
                ), 404);

            if (!Hash::check($request->password, $user->password))
                return response()->json(ApiFormatter::createJson(
                    401, 'Password does not match'
                ), 401);

            $token = JWTAuth::fromUser($user);

            $expires = Carbon::now()
                ->addSeconds(JWTAuth::factory()->getTTL() * 60)
                ->format('Y-m-d H:i:s');

            $data = [
                'type'   => 'Bearer',
                'token'  => $token,
                'expires'=> $expires
            ];

            return response()->json(ApiFormatter::createJson(
                200, 'Login successful', $data
            ), 200);

        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(
                500, 'Internal Server Error', $e->getMessage()
            ), 500);
        }
    }

    public function me()
    {
    try {
        $user = JWTAuth::parseToken()->authenticate();
    } catch (\Exception $e) {
        return response()->json(ApiFormatter::createJson(
            401,
            'Token not provided'
        ), 401);
    }
        $payload = JWTAuth::getPayload(JWTAuth::getToken());

        $data = [
            'name'  => $user->name,
            'email' => $user->email,
            'exp'   => date('Y-m-d H:i:s', $payload->get('exp')),
        ];

        return response()->json(ApiFormatter::createJson(
            200, 'Logged in user', $data
        ), 200);
    }

    public function refresh()
    {
        $expires = Carbon::now()
            ->addSeconds(JWTAuth::factory()->getTTL() * 60)
            ->format('Y-m-d H:i:s');

        $data = [
            'type'   => 'Bearer',
            'token'  => JWTAuth::refresh(),
            'expires'=> $expires
        ];

        return response()->json(ApiFormatter::createJson(
            200, 'Successfully refreshed', $data
        ), 200);
    }

    public function logout()
    {
        $token = JWTAuth::getToken();

        if (!$token)
            return response()->json(ApiFormatter::createJson(
                401, 'Token not provided'
            ), 401);

        JWTAuth::invalidate($token);

        return response()->json(ApiFormatter::createJson(
            200, 'Successfully logged out'
        ), 200);
    }
}
