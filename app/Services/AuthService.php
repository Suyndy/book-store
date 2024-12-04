<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        $user->verify_token = $token;
        $user->save();

        $this->sendVerificationEmail($user, $token);

        return $user;
    }

    protected function sendVerificationEmail(User $user, $token)
    {
        $uiUrl = env('UI_URL', 'http://localhost:5173');

        // Construct the verification link
        $verificationLink = $uiUrl . '/auth/verify-email?token=' . $token . '&email=' . $user->email;

        Mail::to($user->email)->send(new \App\Mail\VerifyEmail($verificationLink));
    }

    protected function sendForgotPasswordEmail(User $user, $token)
    {
        $uiUrl = env('UI_URL', 'http://localhost:5173');

        // Construct the verification link
        $verificationLink = $uiUrl . '/auth/verify-password?token=' . $token . '&email=' . $user->email;

        Mail::to($user->email)->send(new \App\Mail\ForgotPasswordEmail($verificationLink));
    }

    public function verifyEMail($token, $email)
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user || $user->verify_token !== $token || $user->is_active) {
                throw new \Exception('Lỗi thông tin xác thực,');
            }

            $user->is_active = true;
            $user->save();

            return $user;
        } catch (\Exception $e) {
            throw new \Exception('Lỗi gửi email: ' . $e->getMessage());
        }
    }
    

    public function setPassword($data)
    {
        $user = User::where('verify_token', $data["token"])->where('email', $data['email'])->first();

        if (!$user) {
            throw new \Exception('Không tìm thấy tài khoản.');
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return JWTAuth::fromUser($user);
    }

    public function login(array $credentials): ?string
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('Lỗi thông tin xác thực.');
        }

        return JWTAuth::fromUser($user);
    }

    public function refresh(string $token): string
    {
        try {
            return JWTAuth::refresh($token);
        } catch (JWTException $e) {
            throw new \Exception('Lỗi thông tin xác thực.');
        }
    }

    public function forgotPassword(string $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('Không tìm thấy tài khoản.');
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
            throw new \Exception('Không tìm thấy tài khoản.');
        }

        return true; // Return true if verification is successful
    }

    public function resetPassword(string $email, string $password, string $token)
    {
        $user = User::where('email', $email)->first();

        if (!$user || $user->verify_token !== $token || !$user->is_active) {
            throw new \Exception('Không tìm thấy tài khoản.');
        }

        // Hash the new password and save it
        $user->password = Hash::make($password);
        $user->verify_token = null;
        $user->save();

        return true;
    }

    public function loginGoogle(array $userData)
    {
        $user = User::where('email', $userData['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'is_active' => true,
            ]);
        }

        Auth::login($user);

        // Generate token
        $token = JWTAuth::fromUser($user);

        return $token;
    }

}
