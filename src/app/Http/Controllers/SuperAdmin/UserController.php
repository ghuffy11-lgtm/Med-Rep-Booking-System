<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\Schedule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('civil_id', 'like', '%' . $request->search . '%')
                  ->orWhere('company', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->orderBy('name')
            ->paginate(20)
            ->withQueryString();
        
        $roles = ['super_admin', 'pharmacy_admin', 'representative'];
        
        return view('super-admin.users.index', compact('users', 'roles'));
    }
    
    public function create()
    {
        $roles = ['super_admin', 'pharmacy_admin', 'representative'];
        
        return view('super-admin.users.create', compact('roles'));
    }
    
    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);
        
        AuditLogService::log(
            $user,
            'created',
            null,
            $user->toArray(),
            ['created_by' => auth()->user()->name]
        );
        
        return redirect()->route('super-admin.users.index')
            ->with('success', 'User created successfully.');
    }
    
    public function edit(User $user)
    {
        $roles = ['super_admin', 'pharmacy_admin', 'representative'];
        
        return view('super-admin.users.edit', compact('user', 'roles'));
    }
    
    public function update(UserUpdateRequest $request, User $user)
    {
        $oldValues = $user->toArray();
        
        $data = $request->validated();
        
        // Only update password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $user->update($data);
        
        $newValues = $user->fresh()->toArray();
        
        AuditLogService::log(
            $user,
            'updated',
            $oldValues,
            $newValues,
            ['updated_by' => auth()->user()->name]
        );
        
        return redirect()->route('super-admin.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    public function toggleActive(User $user)
    {
        $oldStatus = $user->is_active;
        $user->update(['is_active' => !$user->is_active]);
        
        AuditLogService::log(
            $user,
            $user->is_active ? 'activated' : 'deactivated',
            ['is_active' => $oldStatus],
            ['is_active' => $user->is_active],
            ['toggled_by' => auth()->user()->name]
        );
        
        $message = $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.';
        
        return back()->with('success', $message);
    }
/**
 * Permanently delete a user
 */
public function forceDelete(User $user)
{
    try {
        DB::beginTransaction();

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting super admins (optional security)
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super Admin accounts cannot be deleted.');
        }

        // Store user info for audit log
        $userName = $user->name;
        $userEmail = $user->email;
        $userRole = $user->role;

        // Check if user has bookings
        $bookingCount = $user->bookings()->count();
        $approvedCount = Booking::where('approved_by', $user->id)->count();
        $cancelledCount = Booking::where('cancelled_by', $user->id)->count();

        // Handle foreign key constraints
        // Option 1: Set foreign keys to NULL (preserves booking history)
        Booking::where('approved_by', $user->id)->update(['approved_by' => null]);
        Booking::where('cancelled_by', $user->id)->update(['cancelled_by' => null]);
        Schedule::where('created_by', $user->id)->update(['created_by' => null]);

        // Option 2: Delete user's own bookings (uncomment if you want this)
        // $user->bookings()->delete();

        // Create audit log before deleting
        AuditLogService::log(
            $user,
            'force_deleted',
            $user->toArray(),
            null,
            [
                'deleted_by' => auth()->user()->name,
                'user_name' => $userName,
                'user_email' => $userEmail,
                'user_role' => $userRole,
                'had_bookings' => $bookingCount,
                'had_approved' => $approvedCount,
                'had_cancelled' => $cancelledCount,
                'reason' => 'Permanent deletion by super admin'
            ]
        );

        // Delete the user
        $user->delete();

        DB::commit();

        $message = "User '{$userName}' ({$userEmail}) has been permanently deleted.";
        if ($bookingCount > 0) {
            $message .= " Their {$bookingCount} booking(s) remain in the system for historical records.";
        }

        return redirect()->route('super-admin.users.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('User deletion failed', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
        ]);

        return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
    }
}

}
