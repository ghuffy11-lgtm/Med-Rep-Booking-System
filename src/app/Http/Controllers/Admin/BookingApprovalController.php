<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingApprovalController extends Controller
{
    protected $bookingService;
    
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    
    public function pending()
    {
        $bookings = Booking::pending()
            ->with(['user', 'department'])
            ->orderBy('booking_date')
            ->orderBy('time_slot')
            ->paginate(20);
        
        return view('admin.bookings.pending', compact('bookings'));
    }
    
    public function approve(Booking $booking)
    {
        $this->authorize('approve', $booking);
        
        $result = $this->bookingService->approveBooking($booking, auth()->user());
        
        if ($result['success']) {
            return redirect()->route('admin.bookings.pending')
                ->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
    
    public function reject(Request $request, Booking $booking)
    {
        $this->authorize('reject', $booking);
        
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);
        
        $result = $this->bookingService->rejectBooking(
            $booking,
            auth()->user(),
            $request->rejection_reason
        );
        
        if ($result['success']) {
            return redirect()->route('admin.bookings.pending')
                ->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
    
    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('cancel', $booking);
        
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);
        
        $result = $this->bookingService->cancelBooking(
            $booking,
            auth()->user(),
            $request->cancellation_reason
        );
        
        if ($result['success']) {
            return redirect()->back()
                ->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
}
