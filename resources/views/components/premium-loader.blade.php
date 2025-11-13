@props([
    'message' => 'Loading...',
    'type' => 'spinner', // spinner, dots, pulse, bars
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-8']) }}>
    @if($type === 'spinner')
        {{-- Premium Spinner with Gradient --}}
        <div class="relative w-16 h-16">
            <div class="absolute inset-0 rounded-full border-4 border-blue-100 dark:border-blue-900"></div>
            <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-blue-600 border-r-indigo-600 animate-spin-smooth"></div>
            <div class="absolute inset-2 rounded-full border-4 border-transparent border-t-indigo-500 border-r-purple-500 animate-spin-reverse"></div>
        </div>

    @elseif($type === 'dots')
        {{-- Animated Dots --}}
        <div class="flex gap-2">
            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 animate-bounce-1"></div>
            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 animate-bounce-2"></div>
            <div class="w-3 h-3 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 animate-bounce-3"></div>
        </div>

    @elseif($type === 'pulse')
        {{-- Pulsing Circle --}}
        <div class="relative w-16 h-16">
            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 animate-ping opacity-75"></div>
            <div class="relative rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 w-16 h-16 animate-pulse"></div>
        </div>

    @elseif($type === 'bars')
        {{-- Animated Bars --}}
        <div class="flex items-end gap-1.5 h-16">
            <div class="w-2 bg-gradient-to-t from-blue-600 to-blue-400 rounded-full animate-bar-1"></div>
            <div class="w-2 bg-gradient-to-t from-indigo-600 to-indigo-400 rounded-full animate-bar-2"></div>
            <div class="w-2 bg-gradient-to-t from-purple-600 to-purple-400 rounded-full animate-bar-3"></div>
            <div class="w-2 bg-gradient-to-t from-pink-600 to-pink-400 rounded-full animate-bar-4"></div>
            <div class="w-2 bg-gradient-to-t from-rose-600 to-rose-400 rounded-full animate-bar-5"></div>
        </div>
    @endif

    {{-- Loading Message --}}
    <p class="mt-6 text-sm font-medium text-gray-600 dark:text-gray-400 animate-pulse">
        {{ $message }}
    </p>
</div>

<style>
    @keyframes spin-smooth {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes spin-reverse {
        0% {
            transform: rotate(360deg);
        }
        100% {
            transform: rotate(0deg);
        }
    }

    .animate-spin-smooth {
        animation: spin-smooth 1s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    }

    .animate-spin-reverse {
        animation: spin-reverse 1.5s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    }

    .animate-bounce-1 {
        animation: bounce 1s ease-in-out infinite;
        animation-delay: 0s;
    }

    .animate-bounce-2 {
        animation: bounce 1s ease-in-out infinite;
        animation-delay: 0.2s;
    }

    .animate-bounce-3 {
        animation: bounce 1s ease-in-out infinite;
        animation-delay: 0.4s;
    }

    @keyframes bar {
        0%, 100% {
            height: 20%;
        }
        50% {
            height: 100%;
        }
    }

    .animate-bar-1 {
        animation: bar 1s ease-in-out infinite;
        animation-delay: 0s;
    }

    .animate-bar-2 {
        animation: bar 1s ease-in-out infinite;
        animation-delay: 0.1s;
    }

    .animate-bar-3 {
        animation: bar 1s ease-in-out infinite;
        animation-delay: 0.2s;
    }

    .animate-bar-4 {
        animation: bar 1s ease-in-out infinite;
        animation-delay: 0.3s;
    }

    .animate-bar-5 {
        animation: bar 1s ease-in-out infinite;
        animation-delay: 0.4s;
    }
</style>
