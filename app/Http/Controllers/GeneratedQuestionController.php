<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\GeneratedQuestion;
use App\Services\GroqService;
use Illuminate\Http\RedirectResponse;

class GeneratedQuestionController extends Controller
{

    public function destroy(GeneratedQuestion $generatedQuestion): RedirectResponse
    {
        $this->authorize('delete', $generatedQuestion);

        $generatedQuestion->delete();

        return back()->with('success', 'Generation deleted.');
    }

    public function archivedQuestions()
    {
        $this->authorize('viewAny', GeneratedQuestion::class);

        $archived = GeneratedQuestion::onlyTrashed()
            ->whereHas('concept', function ($query) {
                $query->whereIn('domain_id', auth()->user()->domains()->pluck('id'));
            })
            ->latest()
            ->get();

        return view('generated_questions.archived', compact('archived'));
    }
}
