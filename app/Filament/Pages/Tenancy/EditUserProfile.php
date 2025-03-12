<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Support\Facades\Hash;

class EditUserProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Perfil do Usuário';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações Pessoais')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required(),
                        TextInput::make('current_password')
                            ->label('Senha Atual')
                            ->password()
                            ->dehydrated(false),
                        TextInput::make('new_password')
                            ->label('Nova Senha')
                            ->password()
                            ->dehydrated(false),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirmar Nova Senha')
                            ->password()
                            ->dehydrated(false),
                    ])->columns(2)
            ]);
    }
}