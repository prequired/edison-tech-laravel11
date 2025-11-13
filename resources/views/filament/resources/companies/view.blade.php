<x-filament-panels::page>
    {{-- Premium View Header --}}
    <div class="mb-8 -mt-6 -mx-6 px-6 py-8 bg-gradient-to-br from-emerald-50 via-teal-50 to-blue-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700 edison-bg-mesh">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-3">
                    {{-- Company Logo --}}
                    @if($this->record->logo)
                        <img
                            src="{{ Storage::url($this->record->logo) }}"
                            alt="{{ $this->record->name }}"
                            class="w-16 h-16 rounded-xl object-cover shadow-lg border-2 border-white dark:border-gray-700"
                        />
                    @else
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg border-2 border-white dark:border-gray-700">
                            <span class="text-2xl font-bold text-white">
                                {{ substr($this->record->name, 0, 1) }}
                            </span>
                        </div>
                    @endif

                    {{-- Status Badge --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold {{ $this->record->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800' }}">
                        {{ $this->record->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent tracking-tight">
                    {{ $this->record->name }}
                </h1>

                @if($this->record->email)
                    <p class="mt-2 text-gray-600 dark:text-gray-400 text-lg flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-envelope" class="w-5 h-5" />
                        {{ $this->record->email }}
                    </p>
                @endif
            </div>

            {{-- Header Actions --}}
            <div class="flex gap-3">
                {{ $this->getHeaderActions() }}
            </div>
        </div>

        {{-- Key Metrics --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $metrics = [
                    [
                        'label' => 'Projects',
                        'value' => $this->record->projects()->count(),
                        'icon' => 'heroicon-o-briefcase',
                        'color' => 'blue',
                    ],
                    [
                        'label' => 'Users',
                        'value' => $this->record->users()->count(),
                        'icon' => 'heroicon-o-users',
                        'color' => 'violet',
                    ],
                    [
                        'label' => 'Invoices',
                        'value' => $this->record->invoices()->count(),
                        'icon' => 'heroicon-o-document-text',
                        'color' => 'emerald',
                    ],
                    [
                        'label' => 'Location',
                        'value' => $this->record->city && $this->record->country ? "{$this->record->city}, {$this->record->country}" : 'Not specified',
                        'icon' => 'heroicon-o-map-pin',
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
