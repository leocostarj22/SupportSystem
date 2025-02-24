<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            TicketOverview::class,
            TicketCharts::class,
            QuickActions::class,
        ];
    }
}