<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TodayAppointmentsController extends Controller
{
    /**
     * Display appointments report with date filtering
     */
    public function index(Request $request)
    {
        // Get date filters or default to today
        $fromDate = $request->input('from_date') 
            ? Carbon::parse($request->input('from_date')) 
            : Carbon::today();
        
        $toDate = $request->input('to_date') 
            ? Carbon::parse($request->input('to_date')) 
            : $fromDate->copy();

        // Ensure from_date is not after to_date
        if ($fromDate->gt($toDate)) {
            $temp = $fromDate;
            $fromDate = $toDate;
            $toDate = $temp;
        }

        // Build query
        $appointments = Booking::with(['user', 'department'])
            ->whereBetween('booking_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->where('status', 'approved')
            ->orderBy('booking_date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->get();

        $stats = [
            'total' => $appointments->count(),
            'pharmacy' => $appointments->filter(function($booking) {
                return $booking->department->is_pharmacy_department;
            })->count(),
            'non_pharmacy' => $appointments->filter(function($booking) {
                return !$booking->department->is_pharmacy_department;
            })->count(),
        ];

        return view('admin.reports.today-appointments', compact('appointments', 'stats', 'fromDate', 'toDate'));
    }

    /**
     * Generate PDF of appointments
     */
    public function pdf(Request $request)
    {
        // Get date filters or default to today
        $fromDate = $request->input('from_date') 
            ? Carbon::parse($request->input('from_date')) 
            : Carbon::today();
        
        $toDate = $request->input('to_date') 
            ? Carbon::parse($request->input('to_date')) 
            : $fromDate->copy();

        // Ensure from_date is not after to_date
        if ($fromDate->gt($toDate)) {
            $temp = $fromDate;
            $fromDate = $toDate;
            $toDate = $temp;
        }

        // Build query
        $appointments = Booking::with(['user', 'department'])
            ->whereBetween('booking_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->where('status', 'approved')
            ->orderBy('booking_date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->get();

        $stats = [
            'total' => $appointments->count(),
            'pharmacy' => $appointments->filter(function($booking) {
                return $booking->department->is_pharmacy_department;
            })->count(),
            'non_pharmacy' => $appointments->filter(function($booking) {
                return !$booking->department->is_pharmacy_department;
            })->count(),
        ];

        $pdf = Pdf::loadView('admin.reports.today-appointments-pdf', compact('appointments', 'stats', 'fromDate', 'toDate'))
            ->setPaper('a4', 'landscape');

        $filename = 'appointments_' . $fromDate->format('Y-m-d') . '_to_' . $toDate->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Print view (optimized for printing)
     */
    public function print(Request $request)
    {
        // Get date filters or default to today
        $fromDate = $request->input('from_date') 
            ? Carbon::parse($request->input('from_date')) 
            : Carbon::today();
        
        $toDate = $request->input('to_date') 
            ? Carbon::parse($request->input('to_date')) 
            : $fromDate->copy();

        // Ensure from_date is not after to_date
        if ($fromDate->gt($toDate)) {
            $temp = $fromDate;
            $fromDate = $toDate;
            $toDate = $temp;
        }

        // Build query
        $appointments = Booking::with(['user', 'department'])
            ->whereBetween('booking_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->where('status', 'approved')
            ->orderBy('booking_date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->get();

        $stats = [
            'total' => $appointments->count(),
            'pharmacy' => $appointments->filter(function($booking) {
                return $booking->department->is_pharmacy_department;
            })->count(),
            'non_pharmacy' => $appointments->filter(function($booking) {
                return !$booking->department->is_pharmacy_department;
            })->count(),
        ];

        return view('admin.reports.today-appointments-print', compact('appointments', 'stats', 'fromDate', 'toDate'));
    }
}
