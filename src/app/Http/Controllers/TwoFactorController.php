<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->middleware('auth');
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA setup page
     */
    public function show2FASetup()
    {
        $user = Auth::user();

        // Only Super Admins can access 2FA
        if (!$user->isSuperAdmin()) {
            return redirect()->back()->with('error', '2FA is only available for Super Admins.');
        }

        $recoveryCodes = [];
        if ($user->hasTwoFactorEnabled()) {
            $recoveryCodes = $user->getRecoveryCodes();
        }

        return view('auth.2fa.setup', compact('user', 'recoveryCodes'));
    }

    /**
     * Enable 2FA and generate QR code
     */
    public function enable2FA(Request $request)
    {
        $user = Auth::user();

        if (!$user->isSuperAdmin()) {
            return redirect()->back()->with('error', '2FA is only available for Super Admins.');
        }

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->back()->with('error', '2FA is already enabled.');
        }

        // Generate secret key
        $secret = $this->google2fa->generateSecretKey();
        session(['2fa_temp_secret' => $secret]);

        // Generate QR Code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            )
        );

        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('auth.2fa.enable', compact('secret', 'qrCodeSvg'));
    }

    /**
     * Confirm 2FA setup with verification code
     */
    public function confirm2FA(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|numeric|digits:6',
        ]);

        $user = Auth::user();
        $secret = session('2fa_temp_secret');

        if (!$secret) {
            return redirect()->route('2fa.setup')->with('error', 'Setup session expired. Please try again.');
        }

        // Verify the code
        $valid = $this->google2fa->verifyKey($secret, $request->one_time_password);

        if (!$valid) {
            return redirect()->back()
                ->withErrors(['one_time_password' => 'Invalid verification code. Please try again.'])
                ->withInput();
        }

        // Enable 2FA for user
        $user->enableTwoFactor($secret);

        // Generate recovery codes
        $recoveryCodes = $user->generateRecoveryCodes();

        // Clear temporary session
        session()->forget('2fa_temp_secret');

        return view('auth.2fa.recovery-codes', compact('recoveryCodes'))
            ->with('success', '2FA has been successfully enabled!');
    }

    /**
     * Disable 2FA
     */
    public function disable2FA(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        // Verify password
        if (!\Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Incorrect password.'])
                ->withInput();
        }

        $user->disableTwoFactor();

        return redirect()->route('2fa.setup')->with('success', '2FA has been disabled successfully.');
    }

    /**
     * Show 2FA challenge during login
     */
    public function show2FAChallenge()
    {
        \Log::info('2FA Challenge page accessed');

        // Check if user is authenticated and pending 2FA
        $userId = session('2fa:auth:id');
        $isAuthenticated = Auth::check();

        \Log::info('Session check', [
            'is_authenticated' => $isAuthenticated,
            'auth_user_id' => $isAuthenticated ? Auth::id() : null,
            'session_2fa_id' => $userId,
            '2fa_verified' => session('2fa:verified'),
            'all_session_keys' => array_keys(session()->all())
        ]);

        // Check if user is in 2FA verification state
        if (!$userId && !$isAuthenticated) {
            \Log::info('No 2FA session and not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        \Log::info('Showing 2FA challenge view');
        return view('auth.2fa.challenge');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify2FA(Request $request)
    {
        \Log::info('2FA verification started');

        $request->validate([
            'one_time_password' => 'required|numeric|digits:6',
            'trust_device' => 'nullable|boolean',
        ]);

        // Use the new session key
        $userId = session('2fa:auth:id');
        \Log::info('Looking for user in session', ['user_id' => $userId]);

        if (!$userId) {
            \Log::error('No user ID in session');
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            \Log::error('User not found', ['user_id' => $userId]);
            return redirect()->route('login')->with('error', 'User not found.');
        }

        \Log::info('User found, verifying code', ['user_id' => $user->id]);

        // Verify the code
        $secret = $user->getTwoFactorSecret();
        $valid = $this->google2fa->verifyKey($secret, $request->one_time_password);

        if (!$valid) {
            \Log::info('Invalid 2FA code');
            return redirect()->back()
                ->withErrors(['one_time_password' => 'Invalid verification code. Please try again.'])
                ->withInput();
        }

        \Log::info('2FA code verified successfully');

        // Clear 2FA session data
        session()->forget(['2fa:auth:id', '2fa:auth:remember', '2fa:verified']);

        // Mark 2FA as verified
        $request->session()->put('2fa:verified', true);

        // Trust device if requested
        if ($request->trust_device) {
            \Log::info('Trusting device');
            $deviceToken = $user->trustDevice();
            cookie()->queue('trusted_device', $deviceToken, 43200); // 30 days in minutes
        }

        // Log successful 2FA login
        \App\Services\AuditLogService::log($user->id, 'login_2fa_success', 'User', $user->id);

        // Redirect based on role
        $redirectUrl = match($user->role) {
            'super_admin' => route('super-admin.users.index'),
            'pharmacy_admin' => route('admin.dashboard'),
            'representative' => route('rep.dashboard'),
            default => route('rep.dashboard'),
        };

        \Log::info('Redirecting to dashboard', ['role' => $user->role, 'url' => $redirectUrl]);

        return redirect($redirectUrl)
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Verify recovery code
     */
    public function verifyRecoveryCode(Request $request)
    {
        \Log::info('2FA recovery code verification started');

        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        // Use the new session key
        $userId = session('2fa:auth:id');
        if (!$userId) {
            \Log::error('No user ID in session for recovery code');
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            \Log::error('User not found for recovery code', ['user_id' => $userId]);
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Try to use the recovery code
        if (!$user->useRecoveryCode($request->recovery_code)) {
            \Log::info('Invalid recovery code');
            return redirect()->back()
                ->withErrors(['recovery_code' => 'Invalid recovery code.'])
                ->withInput();
        }

        \Log::info('Recovery code verified successfully');

        // Clear 2FA session data
        session()->forget(['2fa:auth:id', 'Auth:auth:remember', '2fa:verified']);

        // Mark 2FA as verified
        $request->session()->put('2fa:verified', true);

        // Log successful recovery code login
        \App\Services\AuditLogService::log($user->id, 'login_recovery_code', 'User', $user->id);

        // Redirect based on role
        $redirectUrl = match($user->role) {
            'super_admin' => route('super-admin.users.index'),
            'pharmacy_admin' => route('admin.dashboard'),
            'representative' => route('rep.dashboard'),
            default => route('rep.dashboard'),
        };

        \Log::info('Redirecting after recovery code', ['role' => $user->role, 'url' => $redirectUrl]);

        return redirect($redirectUrl)
            ->with('warning', 'You logged in using a recovery code. Please generate new recovery codes in your security settings.');
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        // Verify password
        if (!\Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Incorrect password.'])
                ->withInput();
        }

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->back()->with('error', '2FA is not enabled.');
        }

        $recoveryCodes = $user->generateRecoveryCodes();

        return view('auth.2fa.recovery-codes', compact('recoveryCodes'))
            ->with('success', 'Recovery codes have been regenerated!');
    }

    /**
     * Manage trusted devices
     */
    public function manageTrustedDevices()
    {
        $user = Auth::user();

        if (!$user->isSuperAdmin()) {
            return redirect()->back()->with('error', '2FA is only available for Super Admins.');
        }

        $trustedDevices = $user->trustedDevices()->active()->latest()->get();

        return view('auth.2fa.trusted-devices', compact('trustedDevices'));
    }

    /**
     * Revoke a trusted device
     */
    public function revokeTrustedDevice(Request $request, $deviceId)
    {
        $user = Auth::user();
        $device = $user->trustedDevices()->findOrFail($deviceId);
        $device->delete();

        return redirect()->back()->with('success', 'Device has been revoked successfully.');
    }

    /**
     * Revoke all trusted devices
     */
    public function revokeAllTrustedDevices(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        // Verify password
        if (!\Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Incorrect password.'])
                ->withInput();
        }

        $user->revokeAllTrustedDevices();

        // Remove the current device cookie
        cookie()->queue(cookie()->forget('trusted_device'));

        return redirect()->back()->with('success', 'All trusted devices have been revoked successfully.');
    }
}
