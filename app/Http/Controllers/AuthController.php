<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        $events = \App\Models\Event::where('status_event', 'active')->orderBy('event_date', 'asc')->take(5)->get();
        return view('auth.login', compact('events'));
    }

    public function showRegister()
    {
        $events = \App\Models\Event::where('status_event', 'active')->orderBy('event_date', 'asc')->take(5)->get();
        return view('auth.register', compact('events'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Periksa apakah nomor telepon sudah diverifikasi
            if (is_null($user->phone_verified_at)) {
                Auth::logout();
                
                // Buat OTP baru
                $otp = rand(1000, 9999);
                $user->otp_code = $otp;
                $user->otp_expires_at = now()->addMinutes(5);
                $user->save();

                Log::info("MOCK OTP untuk {$user->phone_number}: {$otp}");
                session()->flash('mock_otp', $otp);
                session()->put('verify_user_id', $user->id);

                return redirect()->route('auth.verify.otp')->with('error', 'Silakan verifikasi nomor telepon Anda terlebih dahulu.');
            }

            $request->session()->regenerate();
            
            $role = $user->role;
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'promotor') {
                return redirect()->route('promotor.dashboard');
            } else {
                return redirect()->route('pembeli.home')->with('success', 'Berhasil masuk!');
            }
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:identities,nik',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otp = rand(1000, 9999);
        
        $user = User::create([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'buyer',
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $user->identity()->create([
            'nik' => $validated['nik'],
            'full_name' => $validated['name'],
        ]);

        Log::info("MOCK OTP untuk {$user->phone_number}: {$otp}");
        session()->flash('mock_otp', $otp);
        session()->put('verify_user_id', $user->id);

        return redirect()->route('auth.verify.otp');
    }

    public function showVerifyOtp()
    {
        if (!session()->has('verify_user_id')) {
            return redirect()->route('auth.login');
        }
        
        $user = User::find(session('verify_user_id'));
        return view('auth.verify_otp', compact('user'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:4']);
        
        $userId = session('verify_user_id');
        if (!$userId) {
            return redirect()->route('auth.login');
        }

        $user = User::find($userId);

        if ($user->otp_code !== $request->otp) {
            session()->flash('mock_otp', $user->otp_code); // Tampilkan mock lagi untuk memudahkan
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta kirim ulang.']);
        }

        // OTP Valid
        $user->phone_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user);
        session()->forget('verify_user_id');
        session()->forget('mock_otp');

        return redirect()->route('pembeli.home')->with('success', 'Registrasi dan verifikasi berhasil, selamat datang!');
    }

    public function resendOtp()
    {
        $userId = session('verify_user_id');
        if (!$userId) {
            return redirect()->route('auth.login');
        }

        $user = User::find($userId);
        $otp = rand(1000, 9999);
        
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        Log::info("MOCK OTP RESEND untuk {$user->phone_number}: {$otp}");
        session()->flash('mock_otp', $otp);

        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
