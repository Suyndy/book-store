<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['only' => ['me', 'logout']]);
    }

    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register($request->all());
            
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $result['user'],
                'token' => $result['token'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function verify(Request $request)
    {
        try {
            $this->authService->verifyEmail($request->token, $request->email);
            return response()->json(['message' => 'Email verified successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'token' => 'required|string',
        ]);

        try {
            $token = $this->authService->setPassword($request->all());
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $token = $this->authService->login($request->only('email', 'password'));
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
    
            if (!$token) {
                return response()->json(['error' => 'Token is required'], 400);
            }
    
            $newToken = $this->authService->refresh($token);
            return response()->json(['token' => $newToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $this->authService->forgotPassword($request->email);
            return response()->json([
                'message' => 'Verification token sent to your email.'
        ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function verifyForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        try {
            $this->authService->verifyForgotPassword($request->token, $request->email);
            return response()->json(['message' => 'Token verified successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'token' => 'required|string',
        ]);

        try {
            $token = $this->authService->resetPassword($request->email, $request->password, $request->token);
            return response()->json(['message' => 'Password reset successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
