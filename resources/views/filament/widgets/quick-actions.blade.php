<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="flex flex-col gap-4">
            @if($canCreateTicket)
                {{-- Ações Rápidas --}}
                <div class="p-2 space-x-2 rounded-lg filament-card">
                    {{ $this->createTicket() }}
                </div>
            @endif

            {{-- Atividades Recentes --}}
            <div class="p-4 rounded-lg filament-card">
                <h3 class="text-lg font-medium">
                    {{ $isAdmin ? 'Atividades Recentes' : 'Minhas Atividades Recentes' }}
                </h3>
                <div class="mt-2 space-y-2">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-center justify-between p-2 text-sm rounded bg-gray-50 dark:bg-gray-800">
                            <span>{{ $activity->title }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="p-2 text-sm text-gray-500 dark:text-gray-400">
                            Nenhuma atividade recente
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            {{-- Tarefas Pendentes --}}
            <div class="p-4 rounded-lg filament-card">
                <h3 class="text-lg font-medium">Minhas Tarefas Pendentes</h3>
                <div class="mt-2 space-y-2">
                    @forelse($pendingTasks as $task)
                        <div class="flex items-center justify-between p-2 text-sm rounded bg-gray-50 dark:bg-gray-800">
                            <span>{{ $task->title }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $task->due_date?->format('d/m/Y') ?? 'Sem prazo' }}</span>
                        </div>
                    @empty
                        <div class="p-2 text-sm text-gray-500 dark:text-gray-400">
                            Nenhuma tarefa pendente
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Performance da Equipe --}}
            <div class="p-4 rounded-lg filament-card">
                <h3 class="text-lg font-medium">
                    {{ $isAdmin ? 'Performance da Equipe' : 'Minha Performance' }} (Este Mês)
                </h3>
                <div class="mt-2 space-y-2">
                    @forelse($teamPerformance as $member => $count)
                        <div class="flex items-center justify-between p-2 text-sm rounded bg-gray-50 dark:bg-gray-800">
                            <span>{{ $member }}</span>
                            <span class="font-medium text-primary-600 dark:text-primary-400">{{ $count }} tickets</span>
                        </div>
                    @empty
                        <div class="p-2 text-sm text-gray-500 dark:text-gray-400">
                            Nenhum ticket resolvido este mês
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>