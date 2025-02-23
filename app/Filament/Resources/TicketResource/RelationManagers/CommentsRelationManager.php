<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Closure;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Comentários';
    
    protected static ?string $recordTitleAttribute = 'comment';
    
    public static function getModelLabel(): string
    {
        return 'Comentário';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('comment')
                    ->required()
                    ->label('Comentário')
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
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Autor'),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comentário')
                    ->html()
                    ->wrap()
                    ->words(30)
                    ->extraAttributes([
                        'class' => 'prose max-w-none',
                    ])
                    ->searchable()
                    ->description(fn ($record) => strip_tags($record->comment))
                    ->tooltip(fn ($record) => strip_tags($record->comment)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10]);
    }
}