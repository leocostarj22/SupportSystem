<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TicketCharts extends ChartWidget
{
    protected static ?string $heading = 'Análise de Tickets';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $tickets = Ticket::query();
        
        // Dados para tickets por categoria
        $byCategory = $tickets->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn ($tickets) => $tickets->count());

        // Dados para tendência semanal
        $weeklyTrend = $tickets->whereBetween('created_at', [now()->subWeeks(4), now()])
            ->get()
            ->groupBy(fn ($ticket) => $ticket->created_at->format('W'))
            ->map(fn ($tickets) => $tickets->count());

        return [
            'datasets' => [
                [
                    'label' => 'Tickets por Categoria',
                    'data' => $byCategory->values()->toArray(),
                    'backgroundColor' => [
                        '#36A2EB',
                        '#FF6384',
                        '#4BC0C0',
                        '#FF9F40',
                        '#9966FF',
                    ],
                ],
            ],
            'labels' => $byCategory->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
// Modo 2 - Ao utilizar o modo 2 (gráfico de linha), você pode substituir a propriedade $labels pelo seguinte código:
/*
<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TicketCharts extends ChartWidget
{
    protected static ?string $heading = 'Análise de Tickets';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $tickets = Ticket::query();
        
        // Get tickets for the last 7 days
        $data = $tickets->whereBetween('created_at', [
                now()->subDays(7)->startOfDay(),
                now()->endOfDay(),
            ])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets por Dia',
                    'data' => $data->pluck('count')->toArray(),
                    'fill' => false,
                    'borderColor' => '#f59e0b',
                    'tension' => 0.1,
                ],
            ],
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
*/