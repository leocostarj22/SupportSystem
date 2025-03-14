<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TicketResource\RelationManagers\CommentsRelationManager;
use Filament\Actions\Action;
// Remove duplicate imports and use correct namespaces
use Mokhosh\FilamentRating\Components\Rating;
use Mokhosh\FilamentRating\Columns\RatingColumn;
//use Filament\Infolists\Infolist;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'open' => 'Open',
                                'in_progress' => 'In Progress',
                                'closed' => 'Closed',
                            ])
                            ->default('open'),
                        Forms\Components\Select::make('priority')
                            ->required()
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                            ])
                            ->default('medium'),
                        // Remove the user_id select field and replace with a hidden field
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                        
                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignedTo', 'name', function ($query) {
                                return $query->whereHas('role', function ($query) {
                                    $query->where('name', 'Support');
                                });
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Atribuir para:')
                            ->nullable(),
                        Forms\Components\Hidden::make('due_date')
                            ->default(now()),

                        Forms\Components\RichEditor::make('comment')
                            ->label('Comment')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'subscript',
                                'superscript',
                                'table',
                                'underline',
                                'undo'
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('ticket-attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull()
                            ->maxLength(65535)
                            ->placeholder('Add your comment here...')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->image(),
                        Forms\Components\Section::make('Avaliação')
                            ->schema([
                                Rating::make('rating')
                                    ->label('Avaliação do Cliente')
                                    ->visible(function ($get) {
                                        return $get('status') === 'closed';
                                    }),
                                Forms\Components\Textarea::make('rating_comment')
                                    ->label('Comentário da Avaliação')
                                    ->visible(function ($get) {
                                        return $get('status') === 'closed';
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->visible(function ($get) {
                                return $get('status') === 'closed';
                            })
                            ->collapsed()
                            ->collapsible(),
                    ])->columns(2),
            ]);
    }
    /*public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('title'),
                ColorEntry::make('color'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('url')
                    ->label('URL')
                    ->columnSpanFull()
                    ->url(fn (Link $record): string => '#' . urlencode($record->url)),
                ImageEntry::make('image'),
            ]);
    }
            */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'open',
                        'warning' => 'in_progress',
                        'success' => 'closed',
                    ]),
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ]),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                /*Tables\Columns\TextColumn::make('comment')
                    ->html()
                    ->wrap()
                    ->words(5)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        return $column->getState();
                
                    }),
                    
                Tables\Columns\ImageColumn::make('image')
                    ->height('100%')
                    ->width('100%'),
                    */
                    
                // In the table method, replace the TextColumn for rating with:
                RatingColumn::make('rating')
                    ->label('Avaliação')
                    ->visible(fn ($record) => $record?->status === 'closed')
                    ->summarize(Average::make()
                        ->label('Média de Avaliações')
                    )
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Assigned To'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->role?->name === 'Super Admin') {
            return $query;
        }

        if ($user->role?->name === 'Support') {
            return $query->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                      ->orWhereNull('assigned_to')
                      ->orWhere('user_id', $user->id);
            });
        }

        // Para clientes, mostrar apenas seus próprios tickets
        return $query->where('user_id', $user->id);
    }

    public static function getActions(): array
    {
        return [
            Actions\Action::make('rate')
                ->label('Avaliar Atendimento')
                ->icon('heroicon-o-star')
                ->visible(fn ($record) => $record->status === 'closed' && $record->rating === null && $record->user_id === auth()->id())
                ->form([
                    Rating::make('rating')
                        ->label('Sua avaliação')
                        ->required(),
                    Forms\Components\Textarea::make('rating_comment')
                        ->label('Comentário (opcional)')
                        ->placeholder('Deixe seu feedback sobre o atendimento...'),
                ])
                ->action(function (array $data, $record): void {
                    $record->update([
                        'rating' => $data['rating'],
                        'rating_comment' => $data['rating_comment'],
                    ]);
                    
                    Notification::make()
                        ->title('Obrigado pela sua avaliação!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
