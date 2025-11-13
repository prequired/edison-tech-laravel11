@extends('layouts.admin')

@section('title', 'Contact Submission')
@section('header', 'Contact Submission')

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white shadow p-6">
                <div class="border-b pb-4 mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $contact->name }}</h3>
                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                        <span>{{ $contact->email }}</span>
                        @if($contact->phone)
                            <span>{{ $contact->phone }}</span>
                        @endif
                    </div>
                    @if($contact->company)
                        <div class="mt-1 text-sm text-gray-500">{{ $contact->company }}</div>
                    @endif
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Message</h4>
                    <div class="prose max-w-none text-gray-900">{!! nl2br(e($contact->message)) !!}</div>
                </div>

                @if($contact->subject)
                    <div class="mt-4 pt-4 border-t">
                        <h4 class="text-sm font-medium text-gray-700">Subject</h4>
                        <p class="mt-1 text-sm text-gray-900">{{ $contact->subject }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                        <dd class="mt-1">
                            @if($contact->status === 'unread')
                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Unread</span>
                            @elseif($contact->status === 'read')
                                <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">Read</span>
                            @elseif($contact->status === 'replied')
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Replied</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Archived</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Submitted</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $contact->created_at->format('M d, Y g:i A') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-lg bg-white shadow p-6 space-y-3">
                <form method="POST" action="{{ route('admin.contacts.status', $contact) }}">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="block w-full rounded-md border-gray-300 mb-3" onchange="this.form.submit()">
                        <option value="unread" {{ $contact->status === 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ $contact->status === 'read' ? 'selected' : '' }}>Read</option>
                        <option value="replied" {{ $contact->status === 'replied' ? 'selected' : '' }}>Replied</option>
                        <option value="archived" {{ $contact->status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </form>

                <a href="mailto:{{ $contact->email }}" class="block w-full text-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Reply via Email</a>

                <form method="POST" action="{{ route('admin.contacts.replied', $contact) }}">
                    @csrf
                    <button type="submit" class="w-full rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-500">Mark as Replied</button>
                </form>

                <form method="POST" action="{{ route('admin.contacts.archive', $contact) }}">
                    @csrf
                    <button type="submit" class="w-full rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-500">Archive</button>
                </form>

                <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this submission?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
