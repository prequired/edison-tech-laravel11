<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

/**
 * Premium Project Progress Widget
 *
 * Visualizes project status distribution with sophisticated chart
 */
class ProjectProgressWidget extends ChartWidget
{
    protected static ?string $heading = 'Project Status Distribution';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $statuses = Project::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        $statusConfig = [
            'planning' => ['label' => 'Planning', 'color' => 'rgba(139, 92, 246, 0.8)'],
            'active' => ['label' => 'Active', 'color' => 'rgba(59, 130, 246, 0.8)'],
            'on_hold' => ['label' => 'On Hold', 'color' => 'rgba(245, 158, 11, 0.8)'],
            'completed' => ['label' => 'Completed', 'color' => 'rgba(16, 185, 129, 0.8)'],
            'cancelled' => ['label' => 'Cancelled', 'color' => 'rgba(239, 68, 68, 0.8)'],
        ];

        foreach ($statusConfig as $status => $config) {
            if (isset($statuses[$status]) && $statuses[$status] > 0) {
                $labels[] = $config['label'];
                $data[] = $statuses[$status];
                $colors[] = $config['color'];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => 'rgba(255, 255, 255, 0.8)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'font' => [
                            'size' => 12,
                            'weight' => '600',
                        ],
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
            ],
            'cutout' => '70%',
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
            ],
        ];
    }
}
