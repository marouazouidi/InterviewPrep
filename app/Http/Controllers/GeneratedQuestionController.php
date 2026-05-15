<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\GeneratedQuestion;
use App\Services\GroqService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GeneratedQuestionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private GroqService $groq) {}

    public function store(Concept $concept): RedirectResponse
    {
        $this->authorize('create', [GeneratedQuestion::class, $concept]);

        if (strlen($concept->explanation) < 50) {
            return back()->with('error', 'The concept explanation is too short (min 50 characters) to generate questions.');
        }

        try {
            $questions = $this->groq->generateQuestions(
                $concept->title,
                $concept->explanation
            );

            $concept->generatedQuestions()->create([
                'questions' => $questions,
            ]);

            return back()->with('success', '5 interview questions generated successfully.');
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
}
