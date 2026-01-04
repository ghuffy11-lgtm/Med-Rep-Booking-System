<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Department;
use App\Models\User;
use App\Services\CooldownCalculatorService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $cooldownService;
    
    public function __construct(CooldownCalculatorService $cooldownService)
    {
        $this->cooldownService = $cooldownService;
    }
    
    public function index()
    {
        $stats = [
            'pending_bookings' => Booking::pending()->count(),
            'today_bookings' => Booking::forDate(Carbon::today())->count(),
            'active_reps' => User::representatives()->active()->count(),
            'departments' => Department::count(),
            'approved_today' => Booking::approved()->forDate(Carbon::today())->count(),
            'total_bookings' => Booking::count(),
        ];
        
        $recentBookings = Booking::with(['user', 'department', 'approver'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $cooldownStats = $this->cooldownService->getCooldownStatistics();
        
        $pendingBookings = Booking::pending()
            ->with(['user', 'department'])
            ->orderBy('booking_date')
            ->orderBy('time_slot')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'stats',
            'recentBookings',
            'cooldownStats',
            'pendingBookings'
        ));
    }
}
