<x-filament-panels::page>
    {{-- Premium Dashboard Header with Gradient --}}
    <div class="mb-8 -mt-6 -mx-6 px-6 py-8 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700 edison-bg-mesh">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent tracking-tight">
                    {{ $this->getTitle() }}
                </h1>
                @if($subheading = $this->getSubheading())
                    <p class="mt-2 text-gray-600 dark:text-gray-400 text-lg">
                        {{ $subheading }}
                    </p>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <x-filament::button
                    color="primary"
                    icon="heroicon-o-plus-circle"
                    tag="a"
                    href="{{ route('filament.admin.resources.projects.create') }}"
                >
                    New Project
                </x-filament::button>

                <x-filament::button
                    color="gray"
                    icon="heroicon-o-document-text"
                    tag="a"
                    href="{{ route('filament.admin.resources.invoices.create') }}"
                    outlined
                >
                    New Invoice
                </x-filament::button>
            </div>
        </div>

        {{-- Premium Stats Bar --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($this->getQuickStats() as $stat)
                <div class="relative overflow-hidden rounded-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-{{ $stat['color'] }}-100 dark:border-{{ $stat['color'] }}-900/30 p-4 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-900/20">
                            <x-filament::icon
                                :icon="$stat['icon']"
                                class="w-6 h-6 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"
                            />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Widgets Grid with Premium Layout --}}
    <div class="space-y-6">
        @foreach ($this->getVisibleWidgets() as $widget)
            @if (is_array($widget))
                {{-- Multi-column widget row --}}
                <div class="grid grid-cols-1 lg:grid-cols-{{ count($widget) }} gap-6">
                    @foreach ($widget as $widgetItem)
                        <div class="fi-wi-wrapper">
                            @livewire(\Livewire\Livewire::getAlias($widgetItem))
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Single widget --}}
                <div class="fi-wi-wrapper">
                    @livewire(\Livewire\Livewire::getAlias($widget))
                </div>
            @endif
        @endforeach
    </div>

    {{-- Premium Footer --}}
    <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
        <p>
            Last updated: <span class="font-semibold">{{ now()->format('F j, Y g:i A') }}</span>
        </p>
        <p class="mt-1">
            <span class="inline-flex items-center gap-1">
                <x-filament::icon icon="heroicon-o-shield-check" class="w-4 h-4 text-green-600" />
                All systems operational
            </span>
        </p>
    </div>

    {{-- Premium Animations --}}
    <style>
        .fi-wi-wrapper {
            animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) backwards;
        }

        .fi-wi-wrapper:nth-child(1) { animation-delay: 0.1s; }
        .fi-wi-wrapper:nth-child(2) { animation-delay: 0.2s; }
        .fi-wi-wrapper:nth-child(3) { animation-delay: 0.3s; }
        .fi-wi-wrapper:nth-child(4) { animation-delay: 0.4s; }

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
