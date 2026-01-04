<?php

namespace App\Http\Controllers\Rep;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingStoreRequest;
use App\Models\Booking;
use App\Models\Department;
use App\Services\BookingService;
use App\Services\CooldownCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;
    protected $cooldownService;
    
    public function __construct(
        BookingService $bookingService,
        CooldownCalculatorService $cooldownService
    ) {
        $this->bookingService = $bookingService;
        $this->cooldownService = $cooldownService;
    }
    
    public function index()
    {
        $user = auth()->user();
        
        $bookings = $user->bookings()
            ->with('department')
            ->orderBy('booking_date', 'desc')
            ->orderBy('time_slot', 'desc')
            ->paginate(20);
        
    $statuses = ['pending', 'approved', 'rejected', 'cancelled'];


        return view('rep.bookings.index', compact('bookings'));
    }
    

public function create()
{
    $user = auth()->user();
    
    // Check cooldown
    $cooldownInfo = $this->cooldownService->getCooldownInfo($user);
    if ($cooldownInfo['in_cooldown']) {
        return redirect()->route('rep.dashboard')
            ->with('error', $cooldownInfo['message']);
    }
    
    // Check pending booking
    if ($user->hasPendingBooking()) {
        return redirect()->route('rep.dashboard')
            ->with('error', 'You already have a pending booking. Please wait for approval or cancel it before creating a new one.');
    }
    
    $departments = Department::active()->orderBy('name')->get();
    $globalConfig = \App\Models\GlobalSlotConfig::current();  // ADD THIS LINE
    $allowedDays = $globalConfig->getAllowedDaysAsNumbers();
    
// ADD THIS
\Log::info('Create Booking View Data', [
    'allowedDays' => $allowedDays,
    'globalConfig_days' => $globalConfig->allowed_days
]);

return view('rep.bookings.create', compact('departments', 'cooldownInfo', 'globalConfig', 'allowedDays')); // ADD THIS LINE

}
    
    public function store(BookingStoreRequest $request)
    {
        $user = auth()->user();
        $department = Department::findOrFail($request->department_id);
        $date = Carbon::parse($request->booking_date);
        $timeSlot = $request->time_slot;
        
        $result = $this->bookingService->createBooking(
            $user,
            $department,
            $date,
            $timeSlot
        );
        
        if ($result['success']) {
            return redirect()->route('rep.dashboard')
                ->with('success', $result['message']);
        }
        
        return back()
            ->withInput()
            ->with('error', $result['message']);
    }
    
    public function cancelPending(Request $request, Booking $booking)
    {
        $this->authorize('delete', $booking);
        
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);
        
        $user = auth()->user();
        
        $result = $this->bookingService->cancelPendingBooking(
            $booking,
            $user,
            $request->cancellation_reason
        );
        
        if ($result['success']) {
            return redirect()->route('rep.dashboard')
                ->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
    
    public function history()
    {
        $user = auth()->user();
        
        $bookings = $user->bookings()
            ->past()
            ->with('department')
            ->orderBy('booking_date', 'desc')
            ->orderBy('time_slot', 'desc')
            ->paginate(20);
        
        return view('rep.bookings.history', compact('bookings'));
    }
}
