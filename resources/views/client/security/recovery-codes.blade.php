@extends('layouts.app')

@section('title', 'Recovery Codes')

@section('header', 'Two-Factor Authentication Recovery Codes')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow sm:rounded-lg">
            <!-- Warning Alert -->
            <div class="border-b border-red-200 bg-red-50 px-4 py-5 sm:px-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Save Your Recovery Codes</h3>
                        <p class="mt-2 text-sm text-red-700">These recovery codes are your backup way to access your account if you lose access to your authenticator app. Store them in a safe place. Do not share them with anyone.</p>
                    </div>
                </div>
            </div>

            <!-- Recovery Codes Display -->
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Your Recovery Codes</h3>
                <p class="text-sm text-gray-500 mb-6">Each code can be used once to access your account if you lose your authenticator app.</p>

                <!-- Codes Grid -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    @if(isset($twoFactor) && $twoFactor->recovery_codes)
                        @php
                            $codes = is_array($twoFactor->recovery_codes) ? $twoFactor->recovery_codes : json_decode($twoFactor->recovery_codes, true);
                        @endphp
                        @foreach($codes as $index => $code)
                            <div class="relative">
                                <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-4 font-mono text-sm">
                                    <span id="code-{{ $index }}" class="text-gray-900">{{ $code }}</span>
                                    <button type="button" onclick="copyCode('{{ $code }}')" class="ml-2 text-indigo-600 hover:text-indigo-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @for($i = 0; $i < 10; $i++)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 font-mono text-sm text-gray-400">
                                •••••••••••••
                            </div>
                        @endfor
                    @endif
                </div>

                <!-- Important Notes -->
                <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">How to Use Recovery Codes</h3>
                            <div class="mt-2 text-sm text-yellow-700 space-y-1">
                                <p>• When prompted for a two-factor code, enter one of these recovery codes instead</p>
                                <p>• Each code can only be used once</p>
                                <p>• After using a code, generate new recovery codes immediately</p>
                                <p>• Keep these codes in a secure location (safe, password manager, etc.)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3 sm:flex sm:space-y-0 sm:space-x-3">
                    <a href="javascript:void(0)" onclick="downloadCodes()" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Download Codes
                    </a>

                    <a href="javascript:void(0)" onclick="window.print()" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H7a2 2 0 01-2-2v-4a2 2 0 012-2h10a2 2 0 012 2v4a2 2 0 01-2 2zm0 0h6a2 2 0 002-2v-4a2 2 0 00-2-2m0 0H9m4 0V5a2 2 0 10-4 0v8m0 0H5" />
                        </svg>
                        Print Codes
                    </a>

                    <form method="POST" action="{{ route('client.security.recovery-codes.regenerate') }}" class="inline" onsubmit="return confirm('Are you sure you want to regenerate your recovery codes? Your old codes will no longer work.');">
                        @csrf
                        @method('POST')
                        <button type="submit" class="inline-flex items-center rounded-md border border-yellow-300 bg-yellow-50 px-4 py-2 text-sm font-medium text-yellow-700 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Regenerate Codes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- What to Do Next -->
        <div class="mt-6 rounded-lg bg-white shadow">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium text-gray-900">What To Do Next</h3>
            </div>
            <div class="px-4 py-5 sm:px-6">
                <ol class="space-y-4 list-decimal list-inside text-sm text-gray-700">
                    <li>
                        <strong>Save these codes</strong> in a secure location such as a password manager, safe, or encrypted file. Do not store them with your authenticator app backup.
                    </li>
                    <li>
                        <strong>Download or print these codes</strong> as a backup in case you need to access them later.
                    </li>
                    <li>
                        <strong>Keep them confidential</strong> and do not share them with anyone. Anyone with these codes can access your account.
                    </li>
                    <li>
                        <strong>Use them wisely</strong> - only use recovery codes when you truly cannot access your authenticator app. Each code is single-use only.
                    </li>
                    <li>
                        <strong>Generate new codes</strong> regularly or whenever you lose one. Don't wait until you're locked out.
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Sidebar Information -->
    <div class="space-y-6">
        <!-- Security Info -->
        <div class="rounded-lg bg-indigo-50 border border-indigo-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H3a1 1 0 00-1 1v12a1 1 0 001 1h14a1 1 0 001-1V4a1 1 0 00-1-1h-2a1 1 0 000 2 2 2 0 012 2v7a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2-1a1 1 0 000 2h6a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-indigo-800">About Recovery Codes</h3>
                    <div class="mt-2 text-sm text-indigo-700 space-y-2">
                        <p>Recovery codes are 8-character codes that serve as a backup method to access your account when you don't have access to your authenticator app.</p>
                        <p>Each code can be used only once. After using a code, it becomes invalid.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Practices -->
        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Best Practices</h3>
                    <ul class="mt-2 text-sm text-green-700 space-y-2">
                        <li class="flex items-start">
                            <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Store in password manager
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Print and store securely
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Never share with anyone
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Regenerate regularly
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('client.security.index') }}" class="block text-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Back to Security Settings
            </a>
        </div>
    </div>
</div>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Recovery code copied to clipboard');
    }).catch(() => {
        alert('Failed to copy recovery code');
    });
}

function downloadCodes() {
    @if(isset($twoFactor) && $twoFactor->recovery_codes)
        @php
            $codes = is_array($twoFactor->recovery_codes) ? $twoFactor->recovery_codes : json_decode($twoFactor->recovery_codes, true);
            $codesText = implode("\n", $codes);
        @endphp
        const codesText = `Two-Factor Authentication Recovery Codes
Generated: {{ now()->format('M d, Y H:i:s') }}
=====================================

@foreach($codes as $code)
{{ $code }}
@endforeach

IMPORTANT: Keep these codes in a safe place. Do not share them with anyone.
Each code can only be used once to access your account.
`;
        const element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(codesText));
        element.setAttribute('download', 'recovery-codes.txt');
        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    @else
        alert('Recovery codes are not available');
    @endif
}
</script>

<style media="print">
    @page {
        margin: 1cm;
    }
    body {
        font-family: monospace;
    }
    .no-print {
        display: none;
    }
</style>
@endsection
