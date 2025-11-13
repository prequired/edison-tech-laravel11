<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Client Ticket Controller
 *
 * Allows clients to create and manage support tickets.
 *
 * NOTE: This controller requires a Ticket model and migration to be created.
 * The Ticket model should have the following structure:
 * - id, company_id, user_id, subject, description, status, priority, assigned_to
 * - category, attachments (json), closed_at, created_at, updated_at
 */
class TicketController extends Controller
{
    /**
     * Display a listing of the client's tickets.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // TODO: Implement when Ticket model is created
        // $companyId = Auth::user()->company_id;
        //
        // $tickets = Ticket::where('company_id', $companyId)
        //     ->with(['user', 'assignedTo'])
        //     ->when($request->filled('status'), function ($query) use ($request) {
        //         $query->where('status', $request->status);
        //     })
        //     ->latest()
        //     ->paginate(15);
        //
        // return view('client.tickets.index', compact('tickets'));

        return view('client.tickets.index', ['tickets' => []]);
    }

    /**
     * Show the form for creating a new ticket.
     *
     * @return View
     */
    public function create(): View
    {
        // TODO: Implement when Ticket model is created
        return view('client.tickets.create');
    }

    /**
     * Store a newly created ticket in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Implement when Ticket model is created
        // $validated = $request->validate([
        //     'subject' => ['required', 'string', 'max:255'],
        //     'description' => ['required', 'string'],
        //     'priority' => ['required', 'in:low,medium,high,urgent'],
        //     'category' => ['nullable', 'string', 'max:100'],
        //     'attachments' => ['nullable', 'array'],
        //     'attachments.*' => ['file', 'max:5120'], // 5MB
        // ]);
        //
        // $validated['company_id'] = Auth::user()->company_id;
        // $validated['user_id'] = Auth::id();
        // $validated['status'] = 'open';
        //
        // if ($request->hasFile('attachments')) {
        //     $attachmentPaths = [];
        //     foreach ($request->file('attachments') as $file) {
        //         $attachmentPaths[] = $file->store('tickets', 'private');
        //     }
        //     $validated['attachments'] = $attachmentPaths;
        // }
        //
        // $ticket = Ticket::create($validated);
        //
        // return redirect()
        //     ->route('client.tickets.show', $ticket)
        //     ->with('success', 'Support ticket created successfully.');

        return redirect()
            ->route('client.tickets.index')
            ->with('info', 'Ticket system is not yet implemented.');
    }

    /**
     * Display the specified ticket.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        // TODO: Implement when Ticket model is created
        // $ticket = Ticket::with(['user', 'assignedTo', 'replies.user'])
        //     ->findOrFail($id);
        //
        // $this->authorize('view', $ticket);
        //
        // return view('client.tickets.show', compact('ticket'));

        return view('client.tickets.show', ['ticket' => null]);
    }

    /**
     * Show the form for editing the specified ticket.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        // TODO: Implement when Ticket model is created
        return view('client.tickets.edit', ['ticket' => null]);
    }

    /**
     * Update the specified ticket in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // TODO: Implement when Ticket model is created
        return redirect()
            ->route('client.tickets.index')
            ->with('info', 'Ticket system is not yet implemented.');
    }

    /**
     * Close the specified ticket.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function close(int $id): RedirectResponse
    {
        // TODO: Implement when Ticket model is created
        return redirect()
            ->route('client.tickets.index')
            ->with('info', 'Ticket system is not yet implemented.');
    }

    /**
     * Add a reply to the ticket.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function reply(Request $request, int $id): RedirectResponse
    {
        // TODO: Implement when Ticket model is created
        return redirect()
            ->route('client.tickets.index')
            ->with('info', 'Ticket system is not yet implemented.');
    }
}
