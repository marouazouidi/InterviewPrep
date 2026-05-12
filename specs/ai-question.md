# Spec — AI Interview Question Generation

> Feature: Automatic question generation via the Groq API
> User Stories covered: **US11, US12, US13**
> Branch: `feature/ai-generation`
> Agent used: Claude Code

---

## Context

This is the differentiating feature of the application. The user has written an explanation of a concept in their own words. They now want to practice with real interview questions.

By clicking "Generate Interview Questions", the app sends the concept's title and explanation to the Groq API and receives 5 realistic technical questions. The questions are saved to the database and can be consulted at any time in the generation history.

---

## User Stories Covered

### US11 — Generate Interview Questions
From the concept detail page, a "Generate Interview Questions" button triggers a Groq API call. 5 realistic technical questions are returned, saved to the database, and immediately displayed.

### US12 — View Generation History
All past generations for a concept are visible on the detail page. Each generation shows its 5 questions and its creation date.

### US13 — Delete a Generation
The user can delete a set of generated questions they no longer need.

---

## What I WANT

- A `app/Services/GroqService.php` service class that encapsulates the API call
- The call via native Laravel `Http::` facade — **zero external packages**
- API key in `.env` only (`GROQ_API_KEY`, `GROQ_MODEL`)
- `GeneratedQuestion` model with `belongsTo(Concept::class)` and a `questions` field of type JSON
- `GeneratedQuestionController` with `store` (triggers generation) and `destroy`
- Generation triggered via a POST form from the `concepts.show` page
- Result saved to the database **before** being displayed
- If the API fails, show a readable flash error message (no blank page, no unhandled exception)
- Questions saved in a `questions` JSON field (array of 5 strings)
- History loaded on `concepts.show` via `$concept->generatedQuestions()->latest()->get()`

---

## What I DO NOT WANT

- No `openai-php/laravel` package or any third-party SDK — native `Http::` only
- No API key hard-coded anywhere in the source code (not even in comments)
- No display of the raw API response — parse the JSON before displaying
- No asynchronous generation (queue) for v1 — the call is synchronous
- No silent timeout — configure an explicit timeout on `Http::` and handle the exception
- No generation if the concept has no explanation (check `strlen($concept->explanation) > 50`)
- No silent cascade delete of generations when a concept is deleted — handle it explicitly via cascade FK

---

## Expected File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── GeneratedQuestionController.php
├── Models/
│   └── GeneratedQuestion.php
└── Services/
    └── GroqService.php

database/migrations/
└── xxxx_create_generated_questions_table.php

resources/views/concepts/
└── show.blade.php  (modified to display generations)

routes/web.php  (add AI routes)

.env.example  (add GROQ_* variables)
```

---

## `generated_questions` Table Schema

```sql
generated_questions (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  questions   JSON NOT NULL,
  concept_id  BIGINT UNSIGNED NOT NULL,
  created_at  TIMESTAMP,
  updated_at  TIMESTAMP,
  FOREIGN KEY (concept_id) REFERENCES concepts(id) ON DELETE CASCADE
)
```

---

## Expected GeneratedQuestion Model

```php
class GeneratedQuestion extends Model
{
    protected $fillable = ['questions', 'concept_id'];

    protected $casts = [
        'questions' => 'array',
    ];

    public function concept(): BelongsTo
    {
        return $this->belongsTo(Concept::class);
    }
}
```

---

## Expected GroqService

```php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
        $this->model  = config('services.groq.model');
    }

    /**
     * Generate 5 interview questions based on a concept title and explanation.
     *
     * @return array<string>  Array of 5 questions
     * @throws \Exception     If the API is unavailable or returns an error
     */
    public function generateInterviewQuestions(string $title, string $explanation): array
    {
        $prompt = $this->buildPrompt($title, $explanation);

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'You are a senior technical recruiter specialized in backend web development. You generate precise, realistic, and varied interview questions.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens'  => 1000,
            ]);

        if (! $response->successful()) {
            throw new \Exception(
                'Groq API error (HTTP ' . $response->status() . '): ' . $response->body()
            );
        }

        $content = $response->json('choices.0.message.content');

        return $this->parseQuestions($content);
    }

    private function buildPrompt(string $title, string $explanation): string
    {
        return <<<EOT
Generate exactly 5 technical interview questions about the following concept.

Concept: {$title}

Explanation:
{$explanation}

Rules:
- Varied questions: definition, practical application, use cases, common pitfalls, comparisons
- Level appropriate for a Laravel/PHP backend developer
- Respond in JSON only, no surrounding text
- Format: ["Question 1?", "Question 2?", "Question 3?", "Question 4?", "Question 5?"]
EOT;
    }

    private function parseQuestions(string $content): array
    {
        $clean = trim($content);

        // Strip backticks if the AI added them
        $clean = preg_replace('/^```json\s*/', '', $clean);
        $clean = preg_replace('/\s*```$/', '', $clean);

        $questions = json_decode($clean, true);

        if (! is_array($questions) || count($questions) !== 5) {
            throw new \Exception('Invalid AI response format — unable to parse questions.');
        }

        return $questions;
    }
}
```

---

## Expected GeneratedQuestionController

```php
class GeneratedQuestionController extends Controller
{
    public function __construct(private GroqService $groq) {}

    public function store(Concept $concept): RedirectResponse
    {
        // Guard: explanation must be long enough
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
        $generatedQuestion->delete();
        return back()->with('success', 'Generation deleted.');
    }
}
```

---

## Configuration in `config/services.php`

```php
'groq' => [
    'key'   => env('GROQ_API_KEY'),
    'model' => env('GROQ_MODEL', 'llama3-70b-8192'),
],
```

---

## Required `.env` Variables

```env
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxx
GROQ_MODEL=llama3-70b-8192
```

**In `.env.example` (no real values):**
```env
GROQ_API_KEY=
GROQ_MODEL=llama3-70b-8192
```

---

## Routes to Add in `web.php`

```php
Route::middleware('auth')->group(function () {
    // ... other routes

    // AI Generation
    Route::post('/concepts/{concept}/questions', [GeneratedQuestionController::class, 'store'])
        ->name('generated-questions.store');

    Route::delete('/generated-questions/{generatedQuestion}', [GeneratedQuestionController::class, 'destroy'])
        ->name('generated-questions.destroy');
});
```

---

## Blade Section in `concepts/show.blade.php`

```blade
{{-- Generation button --}}
<form action="{{ route('generated-questions.store', $concept) }}" method="POST">
    @csrf
    <button type="submit">Generate Interview Questions</button>
</form>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
@endif

{{-- Generation history --}}
@foreach($concept->generatedQuestions()->latest()->get() as $generation)
    <div>
        <p>Generated on {{ $generation->created_at->format('m/d/Y at H:i') }}</p>
        <ol>
            @foreach($generation->questions as $question)
                <li>{{ $question }}</li>
            @endforeach
        </ol>
        <form action="{{ route('generated-questions.destroy', $generation) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">Delete</button>
        </form>
    </div>
@endforeach
```

---

## API Error Handling Table

| Scenario | Expected Behavior |
|---|---|
| Groq API unavailable (30s timeout) | Readable flash error message, redirect back |
| HTTP 429 response (rate limit) | Flash message: "Service temporarily limited, try again in a few seconds" |
| HTTP 401 response (bad key) | Flash message: "Invalid API key — check your configuration" |
| Malformed JSON in response | Flash message: "Unexpected response format" |
| Explanation too short | Flash message before the API call is even made |

---

## Plan Prompt Sent to Agent (Claude Code)

```
Prompt sent to Claude Code in Plan mode:

"In my Laravel 11 app, I want to integrate the Groq API to generate
5 interview questions per concept. The call goes through the native
Http:: facade — zero external packages. The key is in .env only.
I want a GroqService class, a GeneratedQuestionController with
store and destroy, and a GeneratedQuestion model with a JSON cast.
If the API fails, show a clean flash message — never a blank page.
List all files, methods, the prompt I'll send to Groq,
and how to parse the JSON response. Do not generate any code."
```

**Plan output:** The agent correctly structured the GroqService and the JSON parsing. It forgot the `try/catch` in the controller — clarified "error handling is mandatory in store()" and it was corrected. ✅

**What was modified manually:** The prompt sent to Groq — the agent had generated it in French. Rewritten in English and added the constraint to respond in JSON only to simplify parsing. ✅

**What the agent hallucinated:** The agent used `Http::withApiKey()` which does not exist in Laravel. Fixed to `Http::withHeaders(['Authorization' => 'Bearer ...'])`. ✅

---

## Manual Tests Checklist

- [ ] Successful generation → 5 questions displayed on the concept page
- [ ] Questions are saved to the database (visible in DB)
- [ ] Generation on a concept with a short explanation → clean error message
- [ ] Generation with a wrong API key → clean error message (no blank page)
- [ ] History: multiple generations accumulate in descending chronological order
- [ ] Delete a generation → disappears from the list
- [ ] The `questions` field is correctly cast to array (not displayed as raw JSON string)
- [ ] `.env` is not committed — verify with `git status`
- [ ] Debugbar: no N+1 on the show page (generatedQuestions eager loaded)