<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\Auth;

class QuickActions extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';
    protected static ?int $sort = 3;

    public function createTicket(): Action
    {
        return Action::make('createTicket')
            ->label('Novo Ticket')
            ->icon('heroicon-o-plus-circle')
            ->url(route('filament.admin.resources.tickets.create'))
            ->visible(auth()->user()->is_admin)
            ->color('primary');
    }

    protected function getViewData(): array
    {
        $user = Auth::user();
        
        // Para administradores, mostrar todos os tickets
        if ($user->is_admin) {
            $recentActivities = Ticket::with(['user', 'assignedTo'])
                ->latest()
                ->take(5)
                ->get();
                
            $teamPerformance = Ticket::where('status', 'closed')
                ->whereMonth('updated_at', now())
                ->with('assignedTo')
                ->get()
                ->groupBy('assignedTo.name')
                ->map(fn ($tickets) => $tickets->count())
                ->sortDesc();
        } else {
            // Para usuários normais, mostrar apenas tickets relacionados
            $recentActivities = Ticket::with(['user', 'assignedTo'])
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere('assigned_to', $user->id);
                })
                ->latest()
                ->take(5)
                ->get();
                
            $teamPerformance = Ticket::where('status', 'closed')
                ->where('assigned_to', $user->id)
                ->whereMonth('updated_at', now())
                ->get()
                ->groupBy('assignedTo.name')
                ->map(fn ($tickets) => $tickets->count())
                ->sortDesc();
        }

        $pendingTasks = Ticket::where('status', 'open')
            ->where('assigned_to', $user->id)
            ->take(5)
            ->get();

        return [
            'recentActivities' => $recentActivities,
            'pendingTasks' => $pendingTasks,
            'teamPerformance' => $teamPerformance,
            'canCreateTicket' => $user->is_admin,
            'isAdmin' => $user->is_admin,
        ];
    }
}