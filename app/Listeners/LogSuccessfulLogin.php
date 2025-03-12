<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $event->user->update([
            'last_login_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $event->user->id,
            'event' => 'login',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}