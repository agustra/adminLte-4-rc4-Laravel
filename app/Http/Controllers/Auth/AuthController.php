<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\HandleErrors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use HandleErrors;

    public function showLoginForm(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        // dd('test');
        return view('layouts.adminLte.auth.login');
    }

    public function login(Request $request)
    {
        try {

            $loginData = $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            // Log::info('Validation passed', ['email' => $loginData['email']]);

            // Check if user exists
            $user = \App\Models\User::where('email', $loginData['email'])->first();
            if (! $user) {
                Log::warning('User not found', ['email' => $loginData['email']]);

                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan',
                    'errors' => 'User Not Found',
                ], 401);
            }

            // Autentikasi pengguna
            if (Auth::attempt(['email' => $loginData['email'], 'password' => $loginData['password']])) {
                $user = Auth::user();
                // Log::info('Auth::attempt successful', ['user_id' => $user->id]);

                $accessToken = $user->createToken('authToken')->accessToken;
                // Log::info('Access token created');

                return response()->json([
                    'data' => $user,
                    'access_token' => $accessToken,
                    'message' => 'Login berhasil',
                ]);
            }
            Log::error('Auth::attempt failed', ['email' => $loginData['email']]);

            return response()->json([
                'status' => false,
                'message' => 'Email atau password salah',
                'errors' => 'Authentication Error',
            ], 401);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            // Log::info('User logged out', ['user' => $user->email]);
            // Revoke Passport tokens if exists
            if ($user && method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Logout berhasil',
                ]);
            }

            return redirect()->route('login')->with('success', 'Logout berhasil');
        } catch (\Exception $e) {
            Log::error('Logout error', ['error' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Logout gagal',
                ], 500);
            }

            return redirect()->route('login');
        }
    }
}
