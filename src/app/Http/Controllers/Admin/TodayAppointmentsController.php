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
     * Display today's appointments report
     */
    public function index()
    {
        $today = Carbon::today();
        
        $appointments = Booking::with(['user', 'department'])
            ->whereDate('booking_date', $today)
            ->where('status', 'approved')
            ->orderBy('time_slot', 'asc')
            ->get();
        
        $stats = [
            'total' => $appointments->count(),
            'pharmacy' => $appointments->where('department.is_pharmacy_department', true)->count(),
            'non_pharmacy' => $appointments->where('department.is_pharmacy_department', false)->count(),
        ];
        
        return view('admin.reports.today-appointments', compact('appointments', 'stats', 'today'));
    }
    
    /**
     * Generate PDF of today's appointments
     */
    public function pdf()
    {
        $today = Carbon::today();
        
        $appointments = Booking::with(['user', 'department'])
            ->whereDate('booking_date', $today)
            ->where('status', 'approved')
            ->orderBy('time_slot', 'asc')
            ->get();
        
        $stats = [
            'total' => $appointments->count(),
            'pharmacy' => $appointments->where('department.is_pharmacy_department', true)->count(),
            'non_pharmacy' => $appointments->where('department.is_pharmacy_department', false)->count(),
        ];
        
        $pdf = Pdf::loadView('admin.reports.today-appointments-pdf', compact('appointments', 'stats', 'today'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'appointments_' . $today->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Print view (optimized for printing)
     */
    public function print()
    {
        $today = Carbon::today();
        
        $appointments = Booking::with(['user', 'department'])
            ->whereDate('booking_date', $today)
            ->where('status', 'approved')
            ->orderBy('time_slot', 'asc')
            ->get();
        
        $stats = [
            'total' => $appointments->count(),
            'pharmacy' => $appointments->where('department.is_pharmacy_department', true)->count(),
            'non_pharmacy' => $appointments->where('department.is_pharmacy_department', false)->count(),
        ];
        
        return view('admin.reports.today-appointments-print', compact('appointments', 'stats', 'today'));
    }
}
