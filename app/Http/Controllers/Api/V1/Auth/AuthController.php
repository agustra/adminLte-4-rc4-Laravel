<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse, HandleErrors;

    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:55',
                'email' => 'email|required|unique:users',
                'password' => 'required|min:6',
            ]);

            $validatedData['password'] = bcrypt($request->password);
            $user = User::create($validatedData);
            $accessToken = $user->createToken('authToken')->accessToken;

            return $this->createdResponse([
                'user' => $user,
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
            ], 'Registrasi berhasil');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function login(Request $request)
    {
        try {
            $loginData = $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            if (! Auth::attempt($loginData)) {
                return $this->errorResponse('Kredensial tidak valid', 401);
            }

            $user = Auth::user();
            $accessToken = $user->createToken('authToken')->accessToken;

            return $this->successResponse([
                'user' => $user,
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
            ], 'Login berhasil');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();

            return $this->successResponse(null, 'Berhasil logout');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
