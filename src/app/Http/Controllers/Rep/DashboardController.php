<?php

namespace App\Http\Controllers\Rep;

use App\Http\Controllers\Controller;
use App\Services\CooldownCalculatorService;

class DashboardController extends Controller
{
    protected $cooldownService;
    
    public function __construct(CooldownCalculatorService $cooldownService)
    {
        $this->cooldownService = $cooldownService;
    }
    
    public function index()
    {
        $user = auth()->user();
        
        $cooldownInfo = $this->cooldownService->getCooldownInfo($user);
        $pendingBooking = $user->getPendingBooking();
        $upcomingBookings = $user->bookings()
            ->upcoming()
            ->with('department')
            ->orderBy('booking_date')
            ->orderBy('time_slot')
            ->get();
        
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'approved' => $user->bookings()->approved()->count(),
            'pending' => $user->bookings()->pending()->count(),
            'rejected' => $user->bookings()->rejected()->count(),
        ];
        
        return view('rep.dashboard', compact(
            'cooldownInfo',
            'pendingBooking',
            'upcomingBookings',
            'stats'
        ));
    }
}
