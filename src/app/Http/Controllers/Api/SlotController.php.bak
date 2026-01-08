<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            
            $result = $this->bookingService->getAvailableSlots($department, $date);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load available slots.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
