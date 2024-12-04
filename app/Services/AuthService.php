<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Payload;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthService
{

    // public function register(array $data): User
    public function register(array $data): array
    {
        if (User::where('email', $data['email'])->exists()) {
            throw new \Exception('Email already exists.');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $payload = [
            'user_id' => 123,
            'role' => 'admin'
        ];
        
        // Generate token
        $token = $this->generateToken($payload);

        dd($token);

        // $verifyToken = $this->generateCustomJwtToken(['user_id' => $user->id], 60);
        // $user->verify_token = $verifyToken;
        // $user->save();

        // $this->sendVerificationEmail($user, $verifyToken);

        // return $user;
        // return [
        //     'user' => $user,
        //     'token' => $verifyToken,
        // ];
    }

    protected function sendVerificationEmail(User $user, $token)
    {
        $verificationLink = url('/auth/verify?token=' . $token . '&email=' . $user->email);
        Mail::to($user->email)->send(new \App\Mail\VerifyEmail($verificationLink));
    }

    public function verifyEMail($token, $email)
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            
            if (!$payload) {
                throw new \Exception('Invalid token or malformed token.');
            }
    
            // \Log::info('Token Payload: ', $payload->toArray());
            if ($payload['sub'] !== $email) {
                throw new \Exception('Invalid token type.');
            }

            $user = User::where('email', $email)->first();

            if (!$user || $user->verify_token !== $token) {
                throw new \Exception('Invalid user or email mismatch.');
            }

            $user->email_verified_at = now();
            $user->verify_token = null; // Xóa verify_token sau khi xác minh thành công
            $user->save();

    
            return $user;
        } catch (\Exception $e) {
            throw new \Exception('Error verifying email: ' . $e->getMessage());
        }
    }
    

    public function setPassword($data)
    {
        $payload = JWTAuth::setToken($data['token'])->getPayload();

        if (!$payload || $payload['sub'] !== 'custom') {
            throw new \Exception('Invalid token');
        }

        $user = User::where('id', $payload['user_id'])->where('email', $data['email'])->first();

        if (!$user) {
            throw new \Exception('Invalid user or email mismatch.');
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return JWTAuth::fromUser($user);
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

    public function forgotPassword(string $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User not found.');
        }

        // Generate a token with a 60-minute expiration
        $token = $this->generateCustomJwtToken(['user_id' => $user->id], 60);

        // Send verification email with token
        // $this->sendVerificationEmail($user, $token);

        // return true;

        return $token;
    }

    public function verifyForgotPassword(string $token, string $email)
    {
        // Decode the token
        $payload = JWTAuth::setToken($token)->getPayload();

        if (!$payload || $payload['sub'] !== 'custom') {
            throw new \Exception('Invalid token');
        }

        // Retrieve user by token and email
        $user = User::where('id', $payload['user_id'])->where('email', $email)->first();

        if (!$user) {
            throw new \Exception('Invalid user or email mismatch.');
        }

        return true; // Return true if verification is successful
    }

    public function resetPassword(string $email, string $password, string $token)
    {
        // Decode the token
        $payload = JWTAuth::setToken($token)->getPayload();

        if (!$payload || $payload['sub'] !== 'custom') {
            throw new \Exception('Invalid token');
        }

        // Retrieve user by token and email
        $user = User::where('id', $payload['user_id'])->where('email', $email)->first();

        if (!$user) {
            throw new \Exception('Invalid user or email mismatch.');
        }

        // Hash the new password and save it
        $user->password = Hash::make($password);
        $user->save();

        return JWTAuth::fromUser($user); // Return a new token after resetting the password
    }

}
