<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;



class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 5;
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $modelLabel = 'Log de Atividade';
    protected static ?string $pluralModelLabel = 'Logs de Atividades';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Ação')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        'login' => 'info',
                        'logout' => 'gray',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('module')
                    ->label('Módulo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->wrap()
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Data Inicial'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Data Final'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Desde ' . Carbon::parse($data['created_from'])->format('d/m/Y'));
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Até ' . Carbon::parse($data['created_until'])->format('d/m/Y'));
                        }
                        return $indicators;
                    }),
                Tables\Filters\SelectFilter::make('action')
                    ->multiple()
                    ->label('Ação')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'create' => 'Criação',
                        'update' => 'Atualização',
                        'delete' => 'Exclusão',
                    ]),
                Tables\Filters\SelectFilter::make('module')
                    ->multiple()
                    ->label('Módulo')
                    ->options([
                        'auth' => 'Autenticação',
                        'tickets' => 'Tickets',
                        'users' => 'Usuários',
                        'categories' => 'Categorias',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                Action::make('download_json')
                    ->label('Download JSON')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        $query = ActivityLog::with('user');
                        
                        // Apply date filter
                        $dateFilter = $livewire->tableFilters['created_at'] ?? null;
                        if ($dateFilter) {
                            if (!empty($dateFilter['created_from'])) {
                                $query->whereDate('created_at', '>=', $dateFilter['created_from']);
                            }
                            if (!empty($dateFilter['created_until'])) {
                                $query->whereDate('created_at', '<=', $dateFilter['created_until']);
                            }
                        }
                        
                        // Apply action filter
                        $actionFilter = $livewire->tableFilters['action'] ?? null;
                        if ($actionFilter && is_array($actionFilter)) {
                            $query->whereIn('action', array_values($actionFilter));
                        }
                        
                        // Apply module filter
                        $moduleFilter = $livewire->tableFilters['module'] ?? null;
                        if ($moduleFilter && is_array($moduleFilter)) {
                            $query->whereIn('module', array_values($moduleFilter));
                        }
                        
                        $logs = $query->get()
                            ->map(function ($log) {
                                return [
                                    'usuario' => $log->user->name,
                                    'acao' => $log->action,
                                    'modulo' => $log->module,
                                    'descricao' => $log->description,
                                    'ip' => $log->ip_address,
                                    'data' => $log->created_at->format('d/m/Y H:i:s'),
                                ];
                            });
                    
                        $jsonContent = json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        $fileName = 'logs-de-atividades-' . now()->format('d-m-Y') . '.json';
                    
                        return response($jsonContent)
                            ->header('Content-Type', 'application/json')
                            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                    }),
            ])
            ->headerActions([
                Action::make('download_json')
                    ->label('Download JSON')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        $query = ActivityLog::with('user');
                        
                        // Apply date filter
                        $dateFilter = $livewire->tableFilters['created_at'] ?? null;
                        if ($dateFilter) {
                            if (!empty($dateFilter['created_from'])) {
                                $query->whereDate('created_at', '>=', $dateFilter['created_from']);
                            }
                            if (!empty($dateFilter['created_until'])) {
                                $query->whereDate('created_at', '<=', $dateFilter['created_until']);
                            }
                        }
                        
                        // Apply action filter
                        $actionFilter = $livewire->tableFilters['action'] ?? null;
                        if ($actionFilter && is_array($actionFilter)) {
                            $query->whereIn('action', array_values($actionFilter));
                        }
                        
                        // Apply module filter
                        $moduleFilter = $livewire->tableFilters['module'] ?? null;
                        if ($moduleFilter && is_array($moduleFilter)) {
                            $query->whereIn('module', array_values($moduleFilter));
                        }
                        
                        $logs = $query->get()
                            ->map(function ($log) {
                                return [
                                    'usuario' => $log->user->name,
                                    'acao' => $log->action,
                                    'modulo' => $log->module,
                                    'descricao' => $log->description,
                                    'ip' => $log->ip_address,
                                    'data' => $log->created_at->format('d/m/Y H:i:s'),
                                ];
                            });
                    
                        $jsonContent = json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        $fileName = 'logs-de-atividades-' . now()->format('d-m-Y') . '.json';
                    
                        return response($jsonContent)
                            ->header('Content-Type', 'application/json')
                            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                    }),
            ])
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
