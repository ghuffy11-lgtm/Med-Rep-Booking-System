<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SlotController extends Controller
{
    protected $bookingService;
    
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    
    public function getAvailable(Request $request)
    {
        $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
            'date' => 'required|date|after_or_equal:today',
        ]);
        
        try {
            $department = Department::findOrFail($request->department_id);
            $date = Carbon::parse($request->date);
            
            // Log the request for debugging
            Log::info('Slot availability request', [
                'user_id' => auth()->id(),
                'department_id' => $department->id,
                'department_name' => $department->name,
                'is_pharmacy' => $department->is_pharmacy_department,
                'date' => $date->format('Y-m-d')
            ]);
            
            $result = $this->bookingService->getAvailableSlots($department, $date);
            
            // Enhanced logging for debugging slot availability
		Log::info('Slot availability result', [
   		 'total_slots' => $result['total_slots'] ?? 0,
   		 'available_count' => $result['available_count'] ?? 0,
   		 'occupied_count' => $result['occupied_count'] ?? $result['booked_count'] ?? 0,
   		 'slots_detail' => collect($result['slots'] ?? [])->map(function($slot) {
        return [
            'time' => $slot['time'] ?? null,
            'available' => $slot['is_available'] ?? false,
            'booking_id' => $slot['booking_id'] ?? null		
                    ];
                })
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'debug_info' => [
                    'department_type' => $department->is_pharmacy_department ? 'pharmacy' : 'non_pharmacy',
                    'date' => $date->format('Y-m-d'),
                    'user_id' => auth()->id()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to load available slots', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'department_id' => $request->department_id,
                'date' => $request->date
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load available slots.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }
}
