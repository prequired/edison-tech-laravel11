<x-filament-panels::page>
    {{-- Premium List Header with Gradient --}}
    <div class="mb-8 -mt-6 -mx-6 px-6 py-8 bg-gradient-to-br from-emerald-50 via-teal-50 to-blue-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700 edison-bg-mesh">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent tracking-tight">
                    {{ $this->getTitle() }}
                </h1>
                @if($subheading = $this->getSubheading())
                    <p class="mt-2 text-gray-600 dark:text-gray-400 text-lg">
                        {{ $subheading }}
                    </p>
                @endif
            </div>

            {{-- Header Actions --}}
            <div class="flex gap-3">
                {{ $this->getHeaderActions() }}
            </div>
        </div>

        {{-- Quick Company Stats --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $stats = [
                    [
                        'label' => 'Total Companies',
                        'value' => \App\Models\Company::count(),
                        'icon' => 'heroicon-o-building-office-2',
                        'color' => 'blue',
                    ],
                    [
                        'label' => 'Active',
                        'value' => \App\Models\Company::where('is_active', true)->count(),
                        'icon' => 'heroicon-o-check-circle',
                        'color' => 'emerald',
                    ],
                    [
                        'label' => 'Total Projects',
                        'value' => \App\Models\Company::withCount('projects')->get()->sum('projects_count'),
                        'icon' => 'heroicon-o-briefcase',
                        'color' => 'violet',
                    ],
                    [
                        'label' => 'Total Users',
                        'value' => \App\Models\Company::withCount('users')->get()->sum('users_count'),
                        'icon' => 'heroicon-o-users',
                        'color' => 'amber',
                    ],
                ];
            @endphp

            @foreach($stats as $stat)
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

    {{-- Premium Table with Empty State --}}
    @if($this->getTable()->getRecords()->isEmpty() && !$this->getTable()->getFilters())
        <x-empty-state
            icon="heroicon-o-building-office-2"
            title="No companies yet"
            description="Start building your client base by adding your first company. Manage clients, track projects, and grow your business."
            action-label="Add Company"
            :action-url="route('filament.admin.resources.companies.create')"
        />
    @else
        <div class="edison-premium-table">
            {{ $this->table }}
        </div>
    @endif

    {{-- Premium Animations --}}
    <style>
        .edison-premium-table {
            animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) backwards;
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
