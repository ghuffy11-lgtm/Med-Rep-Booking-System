<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AuditLogService;

class ActivateUserOnEmailVerification
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $user = $event->user;

        // If user is not already active, activate them
        if (!$user->is_active) {
            $user->update(['is_active' => true]);

            // Log the activation
            AuditLogService::log(
                $user,
                'activated',
                ['is_active' => false],
                ['is_active' => true],
                [
                    'activated_by' => 'Email Verification',
                    'reason' => 'User verified their email address'
                ]
            );
        }
    }
}
