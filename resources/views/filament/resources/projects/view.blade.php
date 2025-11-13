<x-filament-panels::page>
    {{-- Premium View Header with Project Status --}}
    <div class="mb-8 -mt-6 -mx-6 px-6 py-8 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700 edison-bg-mesh">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-3">
                    @if($this->record->code)
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            {{ $this->record->code }}
                        </span>
                    @endif

                    @php
                        $statusColors = [
                            'planning' => 'blue',
                            'active' => 'green',
                            'on_hold' => 'amber',
                            'completed' => 'emerald',
                            'cancelled' => 'red',
                        ];
                        $statusColor = $statusColors[$this->record->status] ?? 'gray';
                    @endphp

                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-900/30 text-{{ $statusColor }}-700 dark:text-{{ $statusColor }}-300 border border-{{ $statusColor }}-200 dark:border-{{ $statusColor }}-800">
                        {{ ucfirst($this->record->status) }}
                    </span>

                    @if($this->record->priority)
                        @php
                            $priorityColors = [
                                'low' => 'gray',
                                'medium' => 'blue',
                                'high' => 'orange',
                                'urgent' => 'red',
                            ];
                            $priorityColor = $priorityColors[$this->record->priority] ?? 'gray';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-{{ $priorityColor }}-100 dark:bg-{{ $priorityColor }}-900/30 text-{{ $priorityColor }}-700 dark:text-{{ $priorityColor }}-300 border border-{{ $priorityColor }}-200 dark:border-{{ $priorityColor }}-800">
                            {{ ucfirst($this->record->priority) }} Priority
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent tracking-tight">
                    {{ $this->record->name }}
                </h1>

                @if($this->record->company)
                    <p class="mt-2 text-gray-600 dark:text-gray-400 text-lg flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-building-office-2" class="w-5 h-5" />
                        {{ $this->record->company->name }}
                    </p>
                @endif
            </div>

            {{-- Header Actions --}}
            <div class="flex gap-3">
                {{ $this->getHeaderActions() }}
            </div>
        </div>

        {{-- Project Progress Bar --}}
        @if($this->record->progress !== null)
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Project Progress</span>
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $this->record->progress }}%</span>
                </div>
                <div class="w-full h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all duration-1000 ease-out"
                        style="width: {{ $this->record->progress }}%"
                    ></div>
                </div>
            </div>
        @endif

        {{-- Key Metrics --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $metrics = [
                    [
                        'label' => 'Budget',
                        'value' => $this->record->budget ? '$' . number_format($this->record->budget, 2) : 'N/A',
                        'icon' => 'heroicon-o-banknotes',
                        'color' => 'emerald',
                    ],
                    [
                        'label' => 'Start Date',
                        'value' => $this->record->start_date ? $this->record->start_date->format('M j, Y') : 'Not set',
                        'icon' => 'heroicon-o-calendar',
                        'color' => 'blue',
                    ],
                    [
                        'label' => 'End Date',
                        'value' => $this->record->end_date ? $this->record->end_date->format('M j, Y') : 'Not set',
                        'icon' => 'heroicon-o-calendar-days',
                        'color' => 'violet',
                    ],
                    [
                        'label' => 'Type',
                        'value' => ucwords(str_replace('_', ' ', $this->record->type)),
                        'icon' => 'heroicon-o-tag',
                        'color' => 'amber',
                    ],
                ];
            @endphp

            @foreach($metrics as $metric)
                <div class="relative overflow-hidden rounded-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-{{ $metric['color'] }}-100 dark:border-{{ $metric['color'] }}-900/30 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-{{ $metric['color'] }}-50 dark:bg-{{ $metric['color'] }}-900/20">
                            <x-filament::icon
                                :icon="$metric['icon']"
                                class="w-5 h-5 text-{{ $metric['color'] }}-600 dark:text-{{ $metric['color'] }}-400"
                            />
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $metric['label'] }}</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $metric['value'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Premium Infolist --}}
    <div class="edison-premium-content space-y-6">
        {{ $this->getInfolist() }}
    </div>

    {{-- Premium Animations --}}
    <style>
        .edison-premium-content {
            animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) backwards;
            animation-delay: 0.1s;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-filament-panels::page>
