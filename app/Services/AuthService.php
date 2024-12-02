<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;

class AuthService
{
    public function generateCustomJwtToken($customPayload = [], $expirationMinutes = 60)
    {
        // Get the current time
        $issuedAt = now()->timestamp;
    
        // Set the required claims
        $defaultClaims = [
            'iat' => $issuedAt, // Issued at
            'exp' => $issuedAt + ($expirationMinutes * 60), // Expiration time
            'sub' => 'custom', // Subject (can be any identifier)
        ];
    
        // Merge required claims with the custom payload
        $payload = array_merge($defaultClaims, $customPayload);
    // Generate the token
        $token = JWTAuth::factory()->customClaims($payload)->make();
    
        return $token;
    }

    public function register(array $data): User
    {
        if (User::where('email', $data['email'])->exists()) {
            throw new \Exception('Email already exists.');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // $token = $this->generateCustomJwtToken(['userId' -> $user->id], 5); 

        dd($user->id);
        $user->verify_token = $token;
        $user->save();

        // $this->sendSetPasswordEmail($user, $token);
                
        return $user;
    }

    protected function sendSetPasswordEmail(User $user)
    {
        $link = url('/auth/set-password?token=' . $user->reset_password_token);
        \Mail::to($user->email)->send(new \App\Mail\SetPasswordMail($link));
    }

    public function login(array $credentials): ?string
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('Invalid credentials.');
        }

        return JWTAuth::fromUser($user);
    }

    public function refresh(string $token): string
    {
        try {
            return JWTAuth::refresh($token);
        } catch (JWTException $e) {
            throw new \Exception('Invalid token or token expired.');
        }
    }
}
