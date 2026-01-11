<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Models\UserDetail;
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

    public function register(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'mobile' => 'required|digits:10',

            'creating_account_for_me' => 'nullable|boolean',
            'name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',

            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',

            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',

            'current_location' => 'nullable|string|max:255',
            'mother_tongue' => 'nullable|string|max:255',
            'known_languages' => 'nullable|array'
        ]);

        // 🔐 Ensure user exists (OTP + password step must be done)
        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found. Please verify OTP and create password first.'
            ], 404);
        }

        // 🧾 Create or update user details
        $userDetail = UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'creating_account_for_me' => $request->creating_account_for_me,
                'name' => $request->name,
                'dob' => $request->dob,
                'height' => $request->height,
                'weight' => $request->weight,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
                'current_location' => $request->current_location,
                'mother_tongue' => $request->mother_tongue,
                'known_languages' => $request->known_languages
            ]
        );

        return response()->json([
            'message' => 'Registration completed successfully',
            'user' => $user,
            'user_details' => $userDetail
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
