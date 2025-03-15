<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
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
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;



class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $modelLabel = 'Logs de Atividades';
    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'success',
                        'logout' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Endereço IP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Navegador')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('export')
                    ->label('Exportar JSON')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (ActivityLog $record) {
                        $data = [
                            'user_name' => $record->user->name,
                            'event' => $record->event === 'login' ? 'Login' : 'Logout',
                            'created_at' => $record->created_at->format('d/m/Y H:i:s'),
                            'ip_address' => $record->ip_address,
                            'user_agent' => $record->user_agent,
                        ];
                        
                        $filename = 'activity-log-' . now()->format('Y-m-d-H-i-s') . '.json';
                        
                        return response()->streamDownload(function () use ($data) {
                            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }, $filename, [
                            'Content-Type' => 'application/json',
                        ]);
                    })
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportAll')
                    ->label('Exportar JSON')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $filters = $livewire->tableFilters;
                        $query = ActivityLog::query()
                            ->with('user')
                            ->where(function ($query) {
                                $query->where('event', 'login')
                                      ->orWhere('event', 'logout');
                            });
                        
                        if (!empty($filters)) {
                            if (!empty($filters['event']['value'])) {
                                $query->where('event', $filters['event']['value']);
                            }
                            if (!empty($filters['user_id']['value'])) {
                                $query->where('user_id', $filters['user_id']['value']);
                            }
                        }
                        
                        $logs = $query->get()->map(function ($record) {
                            return [
                                'user_name' => $record->user->name,
                                'event' => $record->event === 'login' ? 'Login' : 'Logout',
                                'created_at' => $record->created_at->format('d/m/Y H:i:s'),
                                'ip_address' => $record->ip_address,
                                'user_agent' => $record->user_agent,
                            ];
                        })->toArray();
                        
                        $filename = 'activity-logs-' . now()->format('Y-m-d-H-i-s') . '.json';
                        
                        return response()->streamDownload(function () use ($logs) {
                            echo json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }, $filename, [
                            'Content-Type' => 'application/json',
                            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                        ]);
                    })
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                    ])
                    ->label('Tipo de Evento')
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->where('event', $data['value']);
                        }
                        return $query->where(function ($query) {
                            $query->where('event', 'login')
                                  ->orWhere('event', 'logout');
                        });
                    }),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
