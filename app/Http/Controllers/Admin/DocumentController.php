<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Document Controller
 *
 * Manages document uploads, downloads, and versioning.
 */
class DocumentController extends Controller
{
    /**
     * Display a listing of documents.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Document::class);

        $documents = Document::with(['documentable', 'uploadedBy'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->filled('documentable_type'), function ($query) use ($request) {
                $query->where('documentable_type', $request->documentable_type);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new document.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', Document::class);

        $projects = Project::orderBy('name')->get(['id', 'name']);
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('admin.documents.create', compact('projects', 'companies'));
    }

    /**
     * Store a newly created document in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Document::class);

        $validated = $request->validate([
            'documentable_type' => ['required', 'string', 'in:App\Models\Project,App\Models\Company'],
            'documentable_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:100'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'is_public' => ['boolean'],
            'version' => ['nullable', 'string', 'max:50'],
        ]);

        // Store the file
        $file = $request->file('file');
        $filePath = $file->store('documents', 'private');

        $validated['file_path'] = $filePath;
        $validated['file_name'] = $file->getClientOriginalName();
        $validated['file_size'] = $file->getSize();
        $validated['mime_type'] = $file->getMimeType();
        $validated['uploaded_by'] = Auth::id();

        // If no version specified, set to 1.0
        if (empty($validated['version'])) {
            $validated['version'] = '1.0';
        }

        $document = Document::create($validated);

        return redirect()
            ->route('admin.documents.show', $document)
            ->with('success', 'Document uploaded successfully.');
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

        // Get version history (other documents with same name and documentable)
        $versions = Document::where('name', $document->name)
            ->where('documentable_type', $document->documentable_type)
            ->where('documentable_id', $document->documentable_id)
            ->where('id', '!=', $document->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.documents.show', compact('document', 'versions'));
    }

    /**
     * Show the form for editing the specified document.
     *
     * @param Document $document
     * @return View
     */
    public function edit(Document $document): View
    {
        $this->authorize('update', $document);

        $projects = Project::orderBy('name')->get(['id', 'name']);
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('admin.documents.edit', compact('document', 'projects', 'companies'));
    }

    /**
     * Update the specified document in storage.
     *
     * @param Request $request
     * @param Document $document
     * @return RedirectResponse
     */
    public function update(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:100'],
            'is_public' => ['boolean'],
            'version' => ['nullable', 'string', 'max:50'],
        ]);

        $document->update($validated);

        return redirect()
            ->route('admin.documents.show', $document)
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified document from storage.
     *
     * @param Document $document
     * @return RedirectResponse
     */
    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        // Delete the file from storage
        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Document deleted successfully.');
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

        return Storage::disk('private')->download(
            $document->file_path,
            $document->file_name
        );
    }

    /**
     * Upload a new version of the document.
     *
     * @param Request $request
     * @param Document $document
     * @return RedirectResponse
     */
    public function uploadVersion(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('create', Document::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'version' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        // Store the new file
        $file = $request->file('file');
        $filePath = $file->store('documents', 'private');

        // Create a new document record as a new version
        $newVersion = Document::create([
            'documentable_type' => $document->documentable_type,
            'documentable_id' => $document->documentable_id,
            'name' => $document->name,
            'description' => $validated['description'] ?? $document->description,
            'type' => $document->type,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'version' => $validated['version'],
            'is_public' => $document->is_public,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.documents.show', $newVersion)
            ->with('success', 'New version uploaded successfully.');
    }
}
