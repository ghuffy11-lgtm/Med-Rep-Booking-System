<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Models\Department;
use App\Services\AuditLogService;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('bookings')
            ->orderBy('name')
            ->paginate(20);
        
        return view('admin.departments.index', compact('departments'));
    }
    
    public function create()
    {
        return view('admin.departments.create');
    }
    
    public function store(DepartmentStoreRequest $request)
    {
        $department = Department::create($request->validated());
        
        AuditLogService::log(
            $department,
            'created',
            null,
            $department->toArray(),
            ['created_by' => auth()->user()->name]
        );
        
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }
    
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }
    
    public function update(DepartmentUpdateRequest $request, Department $department)
    {
        $oldValues = $department->toArray();
        
        $department->update($request->validated());
        
        $newValues = $department->fresh()->toArray();
        
        AuditLogService::log(
            $department,
            'updated',
            $oldValues,
            $newValues,
            ['updated_by' => auth()->user()->name]
        );
        
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }
    
    public function destroy(Department $department)
    {
        // Check if department has bookings
        if ($department->bookings()->exists()) {
            return back()->with('error', 'Cannot delete department with existing bookings.');
        }
        
        $departmentData = $department->toArray();
        $department->delete();
        
        AuditLogService::log(
            $department,
            'deleted',
            $departmentData,
            null,
            ['deleted_by' => auth()->user()->name]
        );
        
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
