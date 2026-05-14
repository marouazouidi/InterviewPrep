<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConceptRequest;
use App\Http\Requests\UpdateConceptRequest;
use App\Models\Concept;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConceptController extends Controller
{
    public function create(Domain $domain): View
    {
        $this->authorize('create', [Concept::class, $domain]);
        return view('concepts.create', compact('domain'));
    }

    public function store(StoreConceptRequest $request, Domain $domain): RedirectResponse
    {
        $this->authorize('create', [Concept::class, $domain]);
        $domain->concepts()->create($request->validated());

        return redirect()->route('domains.index', $domain)
            ->with('success', 'Concept created.');
    }

    public function show(Concept $concept): View
    {
        $this->authorize('view', $concept);

        $concept->load([
            'domain',
            'generatedQuestions'
            ]);

        return view('concepts.show', compact('concept'));
    }

    public function edit(Concept $concept): View
    {
        $this->authorize('update', $concept);

        return view('concepts.edit', compact('concept'));
    }

    public function update(UpdateConceptRequest $request, Concept $concept): RedirectResponse
    {
        $this->authorize('update', $concept);

        $concept->update($request->validated());

        return redirect()->route('concepts.index', $concept->domain)
            ->with('success', 'Concept updated.');
    }

    public function archive(Concept $concept): RedirectResponse
    {
        $this->authorize('delete', $concept);

        $concept->delete();

        return back()->with('success', 'Concept archived.');
    }

    public function restore(Concept $concept): RedirectResponse
    {
        $this->authorize('restore', $concept);
        $concept = Concept::onlyTrashed()->findOrFail($concept->id);

        $concept->restore();

        return back()->with('success', 'Concept restored.');
    }

    public function forceDelete(Concept $concept): RedirectResponse
    {
        $this->authorize('forceDelete', $concept);
        $concept = Concept::onlyTrashed()->findOrFail($concept->id);

        $concept->forceDelete();

        return back()->with('success', 'Concept permanently deleted.');
    }

    public function updateStatus(Request $request, Concept $concept): RedirectResponse
    {
        $this->authorize('update', $concept);

        $data = $request->validate([
            'status' => 'required|in:to_review,in_progress,mastered',
        ]);

        $concept->update($data);

        return back()->with('success', 'Status updated.');
    }

    public function archived(): View
    {
        $this->authorize('viewAny', Concept::class);
        $concepts = Concept::onlyTrashed()
            ->whereIn('domain_id', auth()->user()->domains()->pluck('id'))
            ->latest()
            ->get();

        return view('concepts.archived', compact('concepts'));
    }

    public function generateQuestions(Concept $concept): RedirectResponse
    {

    }
}
