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
        // Check if user is in 2FA verification state
        if (!session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa.challenge');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|numeric|digits:6',
            'trust_device' => 'nullable|boolean',
        ]);

        $userId = session('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Verify the code
        $secret = $user->getTwoFactorSecret();
        $valid = $this->google2fa->verifyKey($secret, $request->one_time_password);

        if (!$valid) {
            return redirect()->back()
                ->withErrors(['one_time_password' => 'Invalid verification code. Please try again.'])
                ->withInput();
        }

        // Authentication successful
        session()->forget('2fa:user:id');
        Auth::login($user, session('2fa:remember', false));
        session()->forget('2fa:remember');

        // Trust device if requested
        if ($request->trust_device) {
            $deviceToken = $user->trustDevice();
            cookie()->queue('trusted_device', $deviceToken, 43200); // 30 days in minutes
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Verify recovery code
     */
    public function verifyRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $userId = session('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Try to use the recovery code
        if (!$user->useRecoveryCode($request->recovery_code)) {
            return redirect()->back()
                ->withErrors(['recovery_code' => 'Invalid recovery code.'])
                ->withInput();
        }

        // Authentication successful
        session()->forget('2fa:user:id');
        Auth::login($user, session('2fa:remember', false));
        session()->forget('2fa:remember');

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
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
