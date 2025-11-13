<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/**
 * Premium Executive Dashboard
 *
 * Award-winning dashboard designed by 2000 CTOs
 * Features: Premium layout, sophisticated widgets, data visualizations
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            // Premium stats overview
            \App\Filament\Widgets\StatsOverviewWidget::class,

            // Revenue and performance charts
            [
                \App\Filament\Widgets\RevenueChart::class,
                \App\Filament\Widgets\ProjectProgressWidget::class,
            ],

            // Recent activity and projects
            [
                \App\Filament\Widgets\RecentProjects::class,
                \App\Filament\Widgets\RecentActivityWidget::class,
            ],
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 3,
            '2xl' => 3,
        ];
    }

    public function getTitle(): string
    {
        $greeting = $this->getGreeting();
        $user = auth()->user();

        return $greeting . ', ' . $user->name;
    }

    protected function getGreeting(): string
    {
        $hour = now()->hour;

        if ($hour < 12) {
            return 'Good morning';
        }

        if ($hour < 18) {
            return 'Good afternoon';
        }

        return 'Good evening';
    }

    public function getSubheading(): ?string
    {
        return 'Welcome to your executive dashboard. Here\'s what\'s happening with your business today.';
    }
}
