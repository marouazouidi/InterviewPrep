<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\GeneratedQuestion;
use App\Services\GroqService;
use Illuminate\Http\RedirectResponse;

class GeneratedQuestionController extends Controller
{
    public function __construct(private GroqService $groq) {}

    public function store(Concept $concept): RedirectResponse
    {
        $this->authorize('view', $concept);

        if (strlen($concept->explanation) < 50) {
            return back()->with('error', 'The concept explanation is too short to generate questions.');
        }

        try {
            $questions = $this->groq->generateInterviewQuestions(
                $concept->title,
                $concept->explanation
            );

            $concept->generatedQuestions()->create([
                'questions' => $questions,
            ]);

            return back()->with('success', '5 new questions have been generated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to generate questions: ' . $e->getMessage());
        }
    }

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
