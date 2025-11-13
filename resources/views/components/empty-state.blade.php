@props([
    'title' => 'No items found',
    'description' => 'Get started by creating a new item.',
    'icon' => 'heroicon-o-inbox',
    'action' => null,
    'actionLabel' => 'Create New',
])

<div {{ $attributes->merge(['class' => 'edison-empty-state']) }}>
    <div class="text-center py-16 px-6">
        {{-- Animated Icon Container --}}
        <div class="relative inline-flex items-center justify-center mb-6">
            {{-- Animated Background Circles --}}
            <div class="absolute inset-0 animate-ping-slow opacity-20">
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500"></div>
            </div>
            <div class="absolute inset-0 animate-pulse-slow opacity-30">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-300 to-indigo-400"></div>
            </div>

            {{-- Icon --}}
            <div class="relative z-10 p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 shadow-lg edison-float">
                <x-filament::icon
                    :icon="$icon"
                    class="w-16 h-16 text-blue-600 dark:text-blue-400"
                />
            </div>
        </div>

        {{-- Content --}}
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 edison-text-gradient">
            {{ $title }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-8">
            {{ $description }}
        </p>

        {{-- Action Button --}}
        @if($action)
            <div class="flex justify-center gap-3">
                <x-filament::button
                    :href="$action"
                    color="primary"
                    icon="heroicon-o-plus-circle"
                    size="lg"
                    tag="a"
                >
                    {{ $actionLabel }}
                </x-filament::button>
            </div>
        @endif

        {{-- Decorative Elements --}}
        <div class="mt-12 flex justify-center gap-2">
            <div class="w-2 h-2 rounded-full bg-blue-400 animate-bounce" style="animation-delay: 0s;"></div>
            <div class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 0.2s;"></div>
            <div class="w-2 h-2 rounded-full bg-purple-400 animate-bounce" style="animation-delay: 0.4s;"></div>
        </div>
    </div>
</div>

<style>
    @keyframes ping-slow {
        75%, 100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }

    @keyframes pulse-slow {
        0%, 100% {
            opacity: 0.3;
        }
        50% {
            opacity: 0.5;
        }
    }

    .animate-ping-slow {
        animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .animate-pulse-slow {
        animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .edison-float {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .edison-text-gradient {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
