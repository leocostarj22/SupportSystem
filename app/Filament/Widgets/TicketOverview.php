<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Tickets Ativos', Ticket::where('status', '!=', 'closed')->count())
                ->description('Tickets em andamento')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->url(route('filament.admin.resources.tickets.index', [
                    'tableFilters[status][value]' => 'open'
                ])),

            Stat::make('Alta Prioridade', Ticket::where('priority', 'high')->count())
                ->description('Tickets urgentes')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->url(route('filament.admin.resources.tickets.index', [
                    'tableFilters[priority][value]' => 'high'
                ])),

            Stat::make('Resolvidos Hoje', Ticket::where('status', 'closed')
                ->whereDate('updated_at', today())
                ->count())
                ->description('Tickets finalizados')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.tickets.index', [
                    'tableFilters[status][value]' => 'closed',
                    'tableFilters[updated_at][value]' => 'today'
                ])),

            Stat::make('Tempo Médio de Resposta', function() {
                $avgTime = Ticket::whereNotNull('first_response_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_time')
                    ->value('avg_time');
                return round($avgTime ?? 0) . ' horas';
            })
                ->description('Tempo de primeira resposta')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(route('filament.admin.resources.tickets.index')),
            Stat::make('Satisfação do Cliente', function() {
                $avgRating = Ticket::whereNotNull('rating')
                    ->where('status', 'closed')
                    ->avg('rating');
                
                return number_format($avgRating ?? 0, 1) . ' / 5.0';
            })
                ->description('Média de avaliações')
                ->descriptionIcon('heroicon-o-star')
                ->color('success')
                ->url(route('filament.admin.resources.tickets.index', [
                    'tableFilters[status][value]' => 'closed'
                ])),
            ];
        }
    }