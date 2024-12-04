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
    public function register(array $data): User
    {
        if (User::where('email', $data['email'])->exists()) {
            throw new \Exception('Email already exists.');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Generate token
        $token = Str::random(128);
        // dd($token);

        $user->verify_token = $token;
        $user->save();

        $this->sendVerificationEmail($user, $token);

        return $user;
    }

    protected function sendVerificationEmail(User $user, $token)
    {
        $uiUrl = env('UI_URL', 'http://localhost:3000');

        // Construct the verification link
        $verificationLink = $uiUrl . '/auth/verify-email?token=' . $token . '&email=' . $user->email;

        Mail::to($user->email)->send(new \App\Mail\VerifyEmail($verificationLink));
    }

    protected function sendForgotPasswordEmail(User $user, $token)
    {
        $uiUrl = env('UI_URL', 'http://localhost:3000');

        // Construct the verification link
        $verificationLink = $uiUrl . '/auth/verify-password?token=' . $token . '&email=' . $user->email;

        Mail::to($user->email)->send(new \App\Mail\ForgotPasswordEmail($verificationLink));
    }

    public function verifyEMail($token, $email)
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user || $user->verify_token !== $token || $user->is_active) {
                throw new \Exception('Invalid user or email mismatch.');
            }

            $user->is_active = true;
            $user->verify_token = null;
            $user->save();

            return $user;
        } catch (\Exception $e) {
            throw new \Exception('Error verifying email: ' . $e->getMessage());
        }
    }
    

    public function setPassword($data)
    {
        $user = User::where('verify_token', $data["token"])->where('email', $data['email'])->first();

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
        $token = Str::random(128);
        $user->verify_token = $token;
        $user->save();

        $this->sendForgotPasswordEmail($user, $token);

        // return true;

        return $user;
    }

    public function verifyForgotPassword(string $token, string $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user || $user->verify_token !== $token || !$user->is_active) {
            throw new \Exception('Invalid user or email mismatch.');
        }

        return true; // Return true if verification is successful
    }

    public function resetPassword(string $email, string $password, string $token)
    {
        $user = User::where('email', $email)->first();

        if (!$user || $user->verify_token !== $token || !$user->is_active) {
            throw new \Exception('Invalid user or email mismatch.');
        }

        // Hash the new password and save it
        $user->password = Hash::make($password);
        $user->verify_token = null;
        $user->save();

        return true;
    }

}
