@props([
    'title' => 'Success!',
    'message' => 'Your action was completed successfully.',
])

{{-- Success Celebration Modal/Toast --}}
<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 100)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    {{ $attributes->merge(['class' => 'fixed top-4 right-4 z-50 max-w-md']) }}
>
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 border-2 border-green-200 dark:border-green-700 shadow-2xl backdrop-blur-xl">
        {{-- Animated Background --}}
        <div class="absolute inset-0 opacity-20">
            <div class="confetti-1 absolute top-0 left-0 w-2 h-2 bg-green-400 rounded-full animate-confetti-fall"></div>
            <div class="confetti-2 absolute top-0 left-1/4 w-2 h-2 bg-blue-400 rounded-full animate-confetti-fall" style="animation-delay: 0.2s;"></div>
            <div class="confetti-3 absolute top-0 left-2/4 w-2 h-2 bg-yellow-400 rounded-full animate-confetti-fall" style="animation-delay: 0.4s;"></div>
            <div class="confetti-4 absolute top-0 left-3/4 w-2 h-2 bg-pink-400 rounded-full animate-confetti-fall" style="animation-delay: 0.6s;"></div>
            <div class="confetti-5 absolute top-0 right-0 w-2 h-2 bg-purple-400 rounded-full animate-confetti-fall" style="animation-delay: 0.8s;"></div>
        </div>

        {{-- Content --}}
        <div class="relative p-6">
            <div class="flex items-start gap-4">
                {{-- Success Icon with Animation --}}
                <div class="flex-shrink-0">
                    <div class="relative">
                        <div class="absolute inset-0 animate-ping bg-green-400 rounded-full opacity-75"></div>
                        <div class="relative flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full shadow-lg animate-scale-bounce">
                            <x-filament::icon
                                icon="heroicon-o-check-circle"
                                class="w-7 h-7 text-white"
                            />
                        </div>
                    </div>
                </div>

                {{-- Text Content --}}
                <div class="flex-1 pt-1">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                        {{ $title }}
                    </h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $message }}
                    </p>
                </div>

                {{-- Close Button --}}
                <button
                    @click="show = false"
                    type="button"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                >
                    <x-filament::icon
                        icon="heroicon-o-x-mark"
                        class="w-5 h-5"
                    />
                </button>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="h-1 bg-green-200 dark:bg-green-800">
            <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 animate-progress-bar"></div>
        </div>
    </div>
</div>

<style>
    @keyframes confetti-fall {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(300px) rotate(360deg);
            opacity: 0;
        }
    }

    @keyframes scale-bounce {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    @keyframes progress-bar {
        0% {
            width: 100%;
        }
        100% {
            width: 0%;
        }
    }

    .animate-confetti-fall {
        animation: confetti-fall 2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .animate-scale-bounce {
        animation: scale-bounce 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .animate-progress-bar {
        animation: progress-bar 5s linear forwards;
    }
</style>
