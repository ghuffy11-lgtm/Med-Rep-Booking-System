<?php

namespace App\Http\Controllers\Rep;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('rep.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        // Only validate fields that can be changed
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $oldValues = $user->toArray();

        // Only update company (name and email are read-only)
        $user->company = $validated['company'];

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $newValues = $user->fresh()->toArray();

        // Log the profile update
        AuditLogService::log(
            $user,
            'updated',
            $oldValues,
            $newValues,
            ['context' => 'Profile updated by representative']
        );

        return redirect()->route('rep.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
