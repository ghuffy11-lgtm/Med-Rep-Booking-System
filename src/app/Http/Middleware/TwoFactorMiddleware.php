<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip if user is not authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip if user doesn't have 2FA enabled
        if (!$user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // Skip if already on 2FA challenge page
        if ($request->is('2fa/*')) {
            return $next($request);
        }

        // Check if device is trusted
        $deviceToken = $request->cookie('trusted_device');
        if ($deviceToken && $user->hasDeviceTrusted($deviceToken)) {
            // Update last used time
            $user->updateTrustedDevice($deviceToken);
            return $next($request);
        }

        // Check if 2FA verification is completed this session
        if (session('2fa_verified_' . $user->id)) {
            return $next($request);
        }

        // Redirect to 2FA challenge
        session(['2fa:user:id' => $user->id]);
        Auth::logout();

        return redirect()->route('2fa.challenge')
            ->with('info', 'Please complete two-factor authentication.');
    }
}
