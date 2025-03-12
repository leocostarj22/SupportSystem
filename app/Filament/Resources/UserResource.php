<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->role?->name === 'Super Admin' || $user->hasPermission('view-users'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->role?->name === 'Super Admin' || $user->hasPermission('create-users'));
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->role?->name === 'Super Admin' || $user->hasPermission('edit-users'));
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->role?->name === 'Super Admin' || $user->hasPermission('delete-users'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        // Profile Image field removed from here
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\Select::make('role_id')
                            ->relationship('role', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role.name')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime('d/m/Y H:i:s')
                    ->label('Último Login')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->relationship('role', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\Action::make('change_avatar')
                    ->visible(fn (User $record): bool => 
                        auth()->user()?->role?->name === 'Super Admin' || 
                        auth()->user()?->hasPermission('edit-users')
                    )
                    ->form([
                        Forms\Components\FileUpload::make('new_avatar')
                            ->label('New Profile Image')
                            ->image()
                            ->preserveFilenames()
                            ->maxSize(1024)
                            ->disk('public')
                            ->directory('avatars')
                            ->imageEditor()
                            ->required()
                    ])
                    ->action(function (User $record, array $data): void {
                        if (isset($data['new_avatar'])) {
                            $record->update(['avatar' => $data['new_avatar']]);
                        }
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record): bool => 
                        auth()->user()->role->name === 'Super Admin' || 
                        auth()->user()->hasPermission('edit-users')
                    ),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record): bool => 
                        auth()->user()->role->name === 'Super Admin' || 
                        auth()->user()->hasPermission('delete-users')
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
