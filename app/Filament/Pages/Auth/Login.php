<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getHeading(): string | Htmlable
    {
        return __('Welcome back');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return __('Sign in to your Edison Tech account to continue');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent()
                            ->label('Email address')
                            ->placeholder('Enter your email')
                            ->prefixIcon('heroicon-o-envelope')
                            ->autofocus(),
                        $this->getPasswordFormComponent()
                            ->label('Password')
                            ->placeholder('Enter your password')
                            ->prefixIcon('heroicon-o-lock-closed'),
                        $this->getRememberFormComponent()
                            ->label('Keep me signed in'),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
