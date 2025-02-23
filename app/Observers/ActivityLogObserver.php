<?php

namespace App\Observers;

use App\Models\ActivityLog;

class ActivityLogObserver
{
    public function creating(ActivityLog $activityLog)
    {
        if (auth()->check()) {
            $activityLog->user_id = auth()->id();
            $activityLog->ip_address = request()->ip();
            $activityLog->user_agent = request()->userAgent();
            
            // Valores padrão para campos obrigatórios se não estiverem definidos
            $activityLog->action = $activityLog->action ?? 'view';
            $activityLog->module = $activityLog->module ?? 'system';
            $activityLog->description = $activityLog->description ?? 'Visualização do sistema';
        }
    }
}
