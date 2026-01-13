<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ADD THIS METHOD PROPERLY
    public function showLogin()
    {
        // Detect mobile
        $isMobile = $this->isMobileDevice();
        $userAgent = request()->header('User-Agent');
        
        // Add logging to see what's happening
        \Log::info('Login page accessed', [
            'isMobile' => $isMobile,
            'userAgent' => $userAgent,
            'will_show_view' => $isMobile ? 'login-mobile' : 'login'
        ]);
        
        if ($isMobile) {
            return view('auth.login-mobile');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active && $user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->with('error', 'Your account has been deactivated. Please contact administrator.');

}

// If email not verified yet, redirect to verification notice
if (!$user->hasVerifiedEmail()) {
    Auth::logout();
    return redirect()->route('verification.notice');
}
            
            // Log the login
            AuditLogService::logLogin($user->id);
            
            // Redirect based on role
            return match($user->role) {
                'super_admin' => redirect()->route('super-admin.users.index'),
                'pharmacy_admin' => redirect()->route('admin.dashboard'),
                'representative' => redirect()->route('rep.dashboard'),
                default => redirect()->route('rep.dashboard'),
            };
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }
    
    public function logout(Request $request)
    {
        AuditLogService::logLogout(Auth::id());
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
    return redirect('/login')->with('success', 'You have been logged out successfully.');
}
    
    public function showRegister()
    {
    // Detect mobile
    $isMobile = $this->isMobileDevice();
    
    if ($isMobile) {
        return view('auth.register-mobile');
    }

        return view('auth.register');
    }

    /**
     * Handle registration (hCaptcha + Email Verification)
     */
    public function register(Request $request)
    {
        // Validate input INCLUDING hCaptcha
        $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'password'             => ['required', 'confirmed', Password::min(8)],
            'company'              => 'required|string|max:255',
            'civil_id'             => 'required|string|size:12|unique:users,civil_id|regex:/^[0-9]{12}$/',
            'mobile_number'        => 'required|string|max:20|unique:users,mobile_number',
            'h-captcha-response'   => 'required',
        ], [
            'civil_id.regex'               => 'Civil ID must be exactly 12 digits.',
            'civil_id.size'                => 'Civil ID must be exactly 12 digits.',
            'civil_id.unique'              => 'This Civil ID is already registered.',
            'mobile_number.required'       => 'Mobile number is required.',
            'mobile_number.max'            => 'Mobile number cannot exceed 20 characters.',
            'mobile_number.unique'         => 'This mobile number is already registered.',
            'h-captcha-response.required'  => 'Please complete the captcha verification.',
        ]);

        // Verify hCaptcha
        $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
            'secret'   => config('services.hcaptcha.secret'),
            'response' => $request->input('h-captcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $captchaData = $response->json();

        if (!($captchaData['success'] ?? false)) {
            return back()
                ->withInput()
                ->withErrors([
                    'h-captcha-response' => 'Captcha verification failed. Please try again.',
                ]);
        }

        // Create user (inactive until email verified)
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'company'       => $request->company,
            'civil_id'      => $request->civil_id,
            'mobile_number' => $request->mobile_number,
            'role'          => 'representative',
            'is_active'     => false,
        ]);

        // Login user (will be restricted until verified)
        Auth::login($user);

        // Log registration
//        AuditLogService::log($user, 'created', null, $user->toArray());

        // Send email verification

\Log::info('=== SENDING VERIFICATION EMAIL ===', [
    'user_id' => $user->id,
    'email' => $user->email,
    'user_agent' => $request->header('User-Agent')
]);

        $user->sendEmailVerificationNotification();

\Log::info('=== EMAIL SEND ATTEMPTED ===');

        return redirect()->route('verification.notice')->with(
            'success',
            'Registration successful! Please check your email to verify your account.'
        );
    }


    /**
     * Detect if user is on mobile device
     */
    private function isMobileDevice()
    {
        $userAgent = request()->header('User-Agent');
        return preg_match('/(android|iphone|ipad|mobile|tablet)/i', $userAgent);
    }

    /**
     * Show forgot password form (with mobile detection)
     */
    public function showForgotPassword()
    {
        // Detect mobile
        $isMobile = $this->isMobileDevice();

        if ($isMobile) {
            return view('auth.forgot-password-mobile');
        }

        return view('auth.forgot-password');
    }

    /**
     * Send password reset link (with audit logging)
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'We could not find a user with that email address.',
        ]);

        // Send password reset link
        $status = PasswordBroker::sendResetLink(
            $request->only('email')
        );

        if ($status === PasswordBroker::RESET_LINK_SENT) {
            // Log the password reset request
            \Log::info('Password reset link sent', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            return back()->with('success', 'Password reset link has been sent to your email!');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show reset password form (with mobile detection)
     */
    public function showResetPassword(Request $request, $token)
    {
        // Detect mobile
        $isMobile = $this->isMobileDevice();

        if ($isMobile) {
            return view('auth.reset-password-mobile', [
                'token' => $token,
                'email' => $request->email
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset password (with audit logging)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                // Log the successful password reset
                AuditLogService::log(
                    $user,
                    'password_reset',
                    null,
                    ['reset_at' => now()],
                    ['ip' => request()->ip()]
                );

                \Log::info('Password reset successful', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => request()->ip(),
                ]);
            }
        );

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Your password has been reset successfully! You can now login with your new password.');
        }

        return back()->withErrors(['email' => [__($status)]]);

}
}
