<?php

namespace Webkul\PhoneAuth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Routing\Controller;
use Webkul\PhoneAuth\Models\Customer;
use Webkul\PhoneAuth\Services\OtpService;
use Webkul\PhoneAuth\Repositories\CustomerOtpRepository;
use Webkul\PhoneAuth\Http\Requests\SendOtpRequest;
use Webkul\PhoneAuth\Http\Requests\VerifyOtpRequest;
use Webkul\PhoneAuth\Http\Requests\RegisterRequest;
use Webkul\PhoneAuth\Http\Controllers\Api\Traits\ProvideUser;

class AuthController extends Controller
{
    use ProvideUser;

    protected $otpService;
    protected $otpRepo;

    public function __construct(OtpService $otpService, CustomerOtpRepository $otpRepo)
    {
        $this->otpService = $otpService;
        $this->otpRepo = $otpRepo;
    }

    /**
     * Get the currently authenticated customer (Bagisto compatible).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Webkul\PhoneAuth\Models\Customer|null
     */
    protected function getCurrentCustomer(Request $request)
    {
        return $this->resolveShopUser($request);
    }

    public function sendOtp(SendOtpRequest $request)
    {
        $phone = $request->input('phone');
        $otpModel = $this->otpService->generateOtp($phone);
        // Fire event for extensibility
        Event::dispatch('phoneauth.otp.sent', [$phone, $otpModel]);
        return response()->json(['message' => 'OTP sent successfully.']);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $phone = $request->input('phone');
        $otp = $request->input('otp');
        if ($this->otpService->verifyOtp($phone, $otp)) {
            $customer = Customer::where('phone', $phone)->first();
            if ($customer) {
                $customer->phone_verified = true;
                $customer->phone_verified_at = now();
                $customer->save();
                // Fire Bagisto and custom events
                Event::dispatch('customer.after.verification', [$customer]);
                Event::dispatch('phoneauth.otp.verified', [$phone, $customer]);
                $token = $customer->createToken('api')->plainTextToken;
                return response()->json(['token' => $token, 'customer' => $customer]);
            }
            return response()->json(['message' => 'Phone verified, but customer not found.'], 404);
        }
        // Fire event for failed verification
        Event::dispatch('phoneauth.otp.failed', [$phone, $otp]);
        return response()->json(['message' => 'Invalid or expired OTP.'], 422);
    }

    public function register(RegisterRequest $request)
    {
        $phone = $request->input('phone');
        $customer = Customer::create([
            'first_name' => $request->input('first_name', ''),
            'last_name' => $request->input('last_name', ''),
            'phone' => $phone,
            'phone_verified' => false,
        ]);
        // Fire Bagisto and custom events
        Event::dispatch('customer.registration.after', [$customer]);
        Event::dispatch('phoneauth.customer.registered', [$customer]);
        $otpModel = $this->otpService->generateOtp($phone);
        Event::dispatch('phoneauth.otp.sent', [$phone, $otpModel]);
        return response()->json(['message' => 'Customer registered. OTP sent.', 'customer' => $customer]);
    }

    public function login(SendOtpRequest $request)
    {
        $phone = $request->input('phone');
        $customer = Customer::where('phone', $phone)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }
        // Fire event for login attempt
        Event::dispatch('phoneauth.customer.login_attempt', [$customer]);
        $otpModel = $this->otpService->generateOtp($phone);
        Event::dispatch('phoneauth.otp.sent', [$phone, $otpModel]);
        return response()->json(['message' => 'OTP sent for login.']);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()->delete();
            // Fire event for logout
            Event::dispatch('phoneauth.customer.logged_out', [$user]);
        }
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
