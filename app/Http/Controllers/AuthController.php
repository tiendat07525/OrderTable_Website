<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Carbon\Carbon;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Mail\ForgotPasswordMail;


class AuthController extends Controller
{

    public function showLoginForm()//done
    {
        return view('auth.login');
    }

    public function showRegisterForm()//done
    {
        return view('auth.register');
    }


    public function register(Request $request)//done
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'email' => 'required|email|unique:users',
            'otp' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $otpRecord = EmailOtp::where('email', $request->email)
        ->where('otp', $request->otp)
        ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Mã OTP không hợp lệ'])->withInput();
        }

        if (Carbon::now()->gt($otpRecord->expired_at)) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn'])->withInput();
        }

        $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'email_verified' => true
            ]);

            // Xóa OTP sau khi dùng
            $otpRecord->delete();

            return redirect()
                ->route('auth.login')
                ->with('success', 'Đăng ký thành công');
    }

    public function login(Request $request)//done
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('customer.dashboard');
        }

        return back()->withErrors([
            'login' => 'Tên người dùng hoặc mật khẩu không đúng'
        ]);
    }

    public function logout(Request $request)//done
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ]);

        return back()->with('success', 'Cập nhật thành công');
    }


    public function profile()//done
    {
        $user = auth()->user();

        $completedBookings = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('date', '<', Carbon::today())
            ->count();

        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('date', '>=', Carbon::today())
            ->count();

        $totalSpent = Booking::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        return view('customer.profile', compact(
            'user',
            'completedBookings',
            'upcomingBookings',
            'totalSpent'
        ));
    }
    public function redirectToGoogle()//done
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()//done
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $user = User::create([
                'username' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(Str::random(40)),//random mật khẩu khi đăng nhập qr
                'email_verified' => true
            ]);
        }

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }

    //Gửi OTP

    public function sendOtp(Request $request)//done
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        if (
            EmailOtp::where('email', $request->email)
            ->where('created_at', '>=', now()->subMinute())
            ->exists()
        ) {

            return response()->json([
                'message' => 'Vui lòng chờ 60 giây để gửi lại OTP'
            ], 429);
        }

        $otp = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinutes(5)
            ]
        );

        Mail::to($request->email)
            ->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'OTP đã được gửi'
        ]);
    }

    public function verifyOtp(Request $request)//done
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $otpRecord = EmailOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'OTP không chính xác'
            ], 400);
        }

        if (Carbon::now()->gt($otpRecord->expired_at)) {
            return response()->json([
                'message' => 'OTP đã hết hạn'
            ], 400);
        }

        return response()->json([
            'message' => 'Xác thực email thành công'
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            return back()->withErrors([
                'email' => 'Email không tồn tại'
            ]);
        }

        $newPassword = Str::random(10);

        $user->password = bcrypt($newPassword);

        $user->save();

        Mail::to($user->email)
            ->queue(new ForgotPasswordMail($newPassword));

        return redirect()->route('auth.login')->with(
            'success',
            'Mật khẩu mới đã được gửi qua email'
        );
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {

            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại không đúng'
            ]);
        }

        $user->password = bcrypt($request->new_password);

        $user->save();

        return back()->with(
            'success',
            'Đổi mật khẩu thành công'
        );
    }
}
