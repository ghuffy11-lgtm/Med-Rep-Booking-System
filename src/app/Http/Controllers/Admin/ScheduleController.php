<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleStoreRequest;
use App\Http\Requests\ScheduleUpdateRequest;
use App\Models\Department;
use App\Models\Schedule;
use App\Services\AuditLogService;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['department', 'creator'])
            ->orderBy('start_date', 'desc')
            ->paginate(20);
        
        return view('admin.schedules.index', compact('schedules'));
    }
    
    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];
        
        return view('admin.schedules.create', compact('departments', 'daysOfWeek'));
    }
    
    public function store(ScheduleStoreRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        
        $schedule = Schedule::create($data);
        
        AuditLogService::log(
            $schedule,
            'created',
            null,
            $schedule->toArray(),
            [
                'created_by' => auth()->user()->name,
                'type' => $schedule->is_closed ? 'closure' : 'override'
            ]
        );
        
        return redirect()->route('admin.schedules.index')
            ->with('success', $schedule->is_closed ? 'Closure created successfully.' : 'Schedule override created successfully.');
    }
    
    public function edit(Schedule $schedule)
    {
        $departments = Department::active()->orderBy('name')->get();
        $daysOfWeek = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'
        ];
        
        return view('admin.schedules.edit', compact('schedule', 'departments', 'daysOfWeek'));
    }
    
    public function update(ScheduleUpdateRequest $request, Schedule $schedule)
    {
        $oldValues = $schedule->toArray();
        
        $schedule->update($request->validated());
        
        $newValues = $schedule->fresh()->toArray();
        
        AuditLogService::log(
            $schedule,
            'updated',
            $oldValues,
            $newValues,
            ['updated_by' => auth()->user()->name]
        );
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }
    
    public function destroy(Schedule $schedule)
    {
        $scheduleData = $schedule->toArray();
        $schedule->delete();
        
        AuditLogService::log(
            $schedule,
            'deleted',
            $scheduleData,
            null,
            ['deleted_by' => auth()->user()->name]
        );
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
