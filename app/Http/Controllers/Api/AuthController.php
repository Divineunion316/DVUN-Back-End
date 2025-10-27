<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Contracts\SmsServiceInterface;

class AuthController extends Controller
{

    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }
    
    // 1️⃣ Send OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['mobile' => 'required|digits:10']);

        $otpCode = rand(100000, 999999);

        Otp::updateOrCreate(
            ['mobile' => $request->mobile],
            [
                'otp' => $otpCode,
                'is_verified' => false,
                'expires_at' => Carbon::now()->addMinutes(5)
            ]
        );

        $message = "Your OTP code is {$otpCode}";

        // use the common service
        $this->smsService->send($request->mobile, $message);

        // 👇 Here you can integrate with any SMS provider
        // For now, just return OTP in response
        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => $otpCode // remove in production
        ]);
    }

    // 2️⃣ Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6'
        ]);

        $otpRecord = Otp::where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if ($otpRecord->isExpired()) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        $otpRecord->update(['is_verified' => true]);

        return response()->json(['message' => 'OTP verified successfully']);
    }

    // 3️⃣ Create Password (after OTP verification)
    public function createPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'password' => 'required|min:6'
        ]);

        $otpRecord = Otp::where('mobile', $request->mobile)
            ->where('is_verified', true)
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'OTP not verified yet'], 400);
        }

        $user = User::updateOrCreate(
            ['mobile' => $request->mobile],
            ['password' => Hash::make($request->password)]
        );

        return response()->json(['message' => 'Password set successfully', 'user' => $user]);
    }

    // 4️⃣ Forgot Password → Send OTP (reuse sendOtp)
    // 5️⃣ Forgot Password → Verify OTP → Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6',
            'new_password' => 'required|min:6'
        ]);

        $otpRecord = Otp::where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->where('is_verified', true)
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or unverified OTP'], 400);
        }

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'password' => 'required|min:6'
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid mobile number or password'], 401);
        }

        // Create a new token for API authentication (if using Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }
}
