<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Exception;

/**
 * Class JWTAuthController
 * @group Auth
 *
 * Auth related APIs
 */
class JWTAuthController extends Controller
{
    /**
     * @param UserRegistrationRequest $request
     * handle user registration process using API
     * @return JsonResponse
     */
    public function register(UserRegistrationRequest $request): JsonResponse
    {
        $validated_req = $request->validated();

        $user = User::create([
            'name' => $validated_req['name'],
            'email' => $validated_req['email'],
            'password' => Hash::make($validated_req['password']),
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, 201);
    }

    /**
     * @param UserLoginRequest $request
     * handle user login process using API
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request)
    {
        $credentials = $request->safe()->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {

        try {
            auth('api')->logout();
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }

            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to invalidate token'], 500);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $status = 200)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], $status);
    }
}
