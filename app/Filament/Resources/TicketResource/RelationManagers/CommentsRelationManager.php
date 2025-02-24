<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Contracts\View\View;  // Add this import

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Chat';
    protected static ?string $modelLabel = 'mensagem';
    protected static ?string $pluralModelLabel = 'mensagens';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('comment')
                    ->label('Mensagem')
                    ->required()
                    ->placeholder('Digite sua mensagem...')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'attachFiles',
                    ])
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }
    
    public function table(Table $table): Table
    {
        $createAction = Tables\Actions\CreateAction::make()
            ->label('Enviar')
            ->icon('heroicon-o-paper-airplane')
            ->form([
                Forms\Components\RichEditor::make('comment')
                    ->label('')
                    ->required()
                    ->placeholder('Digite sua mensagem...')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'attachFiles',
                    ])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('files')
                    ->fileAttachmentsVisibility('public')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
            ])
            ->mutateFormDataUsing(function (array $data): array {
                $data['user_id'] = auth()->id();
                return $data;
            });
    return $table
        ->recordTitleAttribute('comment')
        ->headerActions([$createAction])
        ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('user.name')
                        ->weight('bold')
                        ->size('xs')
                        ->color(fn ($record) => $record->user_id === auth()->id() ? 'primary' : 'success'),
                    Tables\Columns\TextColumn::make('comment')
                        ->html()
                        ->wrap()
                        ->alignStart()
                        ->formatStateUsing(fn ($record) => "
                            <div class='flex flex-col" . 
                            ($record->user_id === auth()->id() ? 'items-end' : 'items-start') . "'>
                                <div class='p-2 rounded-lg max-w-[85%] relative group " . 
                                ($record->user_id === auth()->id() ? 
                                'bg-primary-100 text-primary-700' : 
                                'bg-success-50 text-success-700') . "'>
                                    <div class='text-sm'>
                                        <div class='max-w-none prose'>
                                            <div class='[&_figure]:m-0 [&_img]:max-h-[100px] [&_img]:object-contain [&_img]:rounded-lg [&_img]:cursor-pointer'>
                                                " . preg_replace_callback(
                                                    '/<figure(.*?)>(.*?)<img(.*?)src="(.*?)"(.*?)>(.*?)<figcaption.*?>(.*?)<\/figcaption>(.*?)<\/figure>/i',
                                                    function ($matches) {
                                                        $fileExtension = strtolower(pathinfo($matches[7], PATHINFO_EXTENSION));
                                                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        
                                                        // Extract only the relative path from the full URL
                                                        $path = preg_replace('/.*(public\/images\/[^"]+)/', '$1', $matches[4]);
                                                        
                                                        if ($isImage) {
                                                            return '<figure' . $matches[1] . '>' . $matches[2] . 
                                                                   '<img' . $matches[3] . 'src="' . asset('storage/' . $path) . '"' . $matches[5] . 
                                                                   ' onclick="window.dispatchEvent(new CustomEvent(\'open-modal\', { detail: { image: \'' . 
                                                                   asset('storage/' . $path) . '\' } }))">' .
                                                                   '<div class="mt-1 text-xs opacity-70">' . $matches[7] . '</div>' . $matches[8] . '</figure>';
                                                        } else {
                                                            return '<div class="flex gap-2 items-center p-2 bg-gray-50 rounded">
                                                                   <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                   </svg>
                                                                   <a href="' . asset('storage/' . $path) . '" target="_blank" class="text-sm hover:underline">
                                                                       ' . $matches[7] . '
                                                                   </a>
                                                               </div>';
                                                        }
                                                    },
                                                    $record->comment
                                                ) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='flex justify-between items-center'>
                                        <div class='text-[10px] opacity-70 mt-1'>
                                            {$record->created_at->format('H:i')}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        "),
                ])->space(1),
            ])
            ->defaultSort('created_at', 'asc')
            ->contentGrid([
                'md' => 1,
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Enviar')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        Forms\Components\RichEditor::make('comment')
                            ->label('')
                            ->required()
                            ->placeholder('Digite sua mensagem...')
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
                            ->fileAttachmentsDirectory('public/images')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull()
                            ->maxLength(65535)
                            ->placeholder('Add your comment here...')
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->visible(fn ($record) => $record->user_id === auth()->id())
                    ->mutateRecordDataUsing(function (array $data): array {
                        // Fix the image URLs in the content
                        $baseUrl = rtrim(asset('storage'), '/');
                        $data['comment'] = preg_replace(
                            [
                                '/"href":"[^"]+\/storage\/([^"]+)"/',
                                '/"url":"[^"]+\/storage\/([^"]+)"/',
                                '/src="[^"]+\/storage\/([^"]+)"/'
                            ],
                            [
                                '"href":"' . $baseUrl . '/$1"',
                                '"url":"' . $baseUrl . '/$1"',
                                'src="' . $baseUrl . '/$1"'
                            ],
                            $data['comment']
                        );
                        return $data;
                    })
                    ->modalHeading(fn ($record) => 'Editar mensagem')
                    ->slideOver(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->visible(fn ($record) => $record->user_id === auth()->id())
                    ->requiresConfirmation()
                    ->modalHeading('Excluir mensagem')
                    ->modalDescription('Tem certeza que deseja excluir esta mensagem?')
                    ->modalSubmitActionLabel('Sim, excluir')
                    ->modalCancelActionLabel('Cancelar'),
            ])
            ->poll('3s')
        ->paginated(false);
}
protected function getTableContentFooter(): ?View
{
    return view('filament.components.image-modal');
}
}