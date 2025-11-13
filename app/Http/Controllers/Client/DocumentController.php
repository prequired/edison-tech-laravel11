<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Client Document Controller
 *
 * Allows clients to view and download their documents.
 */
class DocumentController extends Controller
{
    /**
     * Display a listing of the client's documents.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $companyId = Auth::user()->company_id;

        // Get all documents for the company and its projects
        $documents = Document::query()
            ->where(function ($query) use ($companyId) {
                // Company documents
                $query->where('documentable_type', 'App\Models\Company')
                    ->where('documentable_id', $companyId);
            })
            ->orWhereHasMorph('documentable', [Project::class], function ($query) use ($companyId) {
                // Project documents for this company
                $query->where('company_id', $companyId);
            })
            ->with(['documentable', 'uploadedBy'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('client.documents.index', compact('documents'));
    }

    /**
     * Display the specified document.
     *
     * @param Document $document
     * @return View
     */
    public function show(Document $document): View
    {
        $this->authorize('view', $document);

        $document->load(['documentable', 'uploadedBy']);

        // Get version history
        $versions = Document::where('name', $document->name)
            ->where('documentable_type', $document->documentable_type)
            ->where('documentable_id', $document->documentable_id)
            ->where('id', '!=', $document->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.documents.show', compact('document', 'versions'));
    }

    /**
     * Download the specified document.
     *
     * @param Document $document
     * @return StreamedResponse
     */
    public function download(Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        // Check if the document belongs to the client's company
        $companyId = Auth::user()->company_id;

        $hasAccess = false;

        if ($document->documentable_type === 'App\Models\Company' && $document->documentable_id === $companyId) {
            $hasAccess = true;
        } elseif ($document->documentable_type === 'App\Models\Project') {
            $project = $document->documentable;
            if ($project && $project->company_id === $companyId) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            abort(403, 'You do not have access to this document.');
        }

        return Storage::disk('private')->download(
            $document->file_path,
            $document->file_name
        );
    }
}
