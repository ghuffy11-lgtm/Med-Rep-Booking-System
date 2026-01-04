<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Department;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'department', 'approver', 'canceller']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }
        
        // Filter by user search
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('civil_id', 'like', '%' . $request->search . '%');
            });
        }
        
        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('time_slot', 'desc')
            ->paginate(20)
            ->withQueryString();
        
        $departments = Department::active()->orderBy('name')->get();
        $statuses = ['pending', 'approved', 'rejected', 'cancelled'];
        
        return view('admin.bookings.index', compact('bookings', 'departments', 'statuses'));
    }
}
