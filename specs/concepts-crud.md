# Spec — Concept CRUD

> Feature: Technical concept management per domain
> User Stories covered: **US5, US6, US7, US8, US9, US10**
> Branch: `feature/concepts-crud`
> Agent used: OpenCode + Claude Code (quick status update)

---

## Context

The concept is the core unit of the application. It is a technical notion the user wants to master — for example "Eloquent N+1 Problem", "Polymorphic Relations", "Laravel Queues", "MySQL Index". Each concept belongs to a domain, has a difficulty level, a mastery status, and an explanation written by the user in their own words.

The main UX goal: let the user quickly see their preparation state and update their progress without friction.

---

## User Stories Covered

### US5 — Concept List for a Domain
- Shows all concepts of a domain with title, formatted difficulty, formatted status
- Filter by status (to review / in progress / mastered)
- Filter by difficulty (junior / mid / senior)
- Both filters can be combined simultaneously (Bonus)

### US6 — Create a Concept
Form with:
- `title`: concept name (e.g. "Eloquent N+1 Problem")
- `explanation`: explanation written by the user (long textarea)
- `difficulty`: `junior` / `mid` / `senior`
- `status`: initialized to `to_review` by default (not selectable on creation)

### US7 — View Concept Detail
Detail page showing: title, full explanation, formatted difficulty, formatted status, and AI-generated question history.

### US8 — Edit a Concept
Edit title, explanation, difficulty, or status.

### US9 — Quick Status Change
From the concept list, a button or select to cycle the status without opening the edit form. Cycle: `to_review → in_progress → mastered → to_review`.

### US10 — Delete a Concept
Soft delete: archive instead of permanently deleting. An "Archived" page allows restore. (Bonus integrated into this CRUD)

---

## What I WANT

- `ConceptController` with: index, create, store, show, edit, update, destroy, updateStatus, archived, restore
- Resourceful route + custom routes for `updateStatus`, `archived`, `restore`
- Form Requests: `StoreConceptRequest` and `UpdateConceptRequest`
- `Concept` model with:
  - `SoftDeletes` trait
  - `belongsTo(Domain::class)`
  - `hasMany(GeneratedQuestion::class)`
  - Accessor `statusLabel()` returning "To Review" / "In Progress" / "Mastered"
  - Accessor `difficultyLabel()` returning "Junior" / "Mid" / "Senior"
- Filters passed as GET params `?status=mastered&difficulty=junior` — query adapts if one or both are present
- Quick status change (US9) via `PATCH /concepts/{id}/status` using a mini-form in the list
- Views in `resources/views/concepts/`: index, create, edit, show, archived

---

## What I DO NOT WANT

- No `status` field selectable on creation — always initialized to `to_review`
- No permanent deletion directly from the list — soft delete only
- No complex JavaScript for filters — a simple GET form is enough
- No pagination for v1
- No Accessor using the old `get...Attribute()` syntax — use `Attribute::make()` (Laravel 9+)
- No access to other users' concepts — always verify via the `domain->user_id` relationship

---

## Expected File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── ConceptController.php
│   └── Requests/
│       ├── StoreConceptRequest.php
│       └── UpdateConceptRequest.php
├── Models/
│   └── Concept.php

database/migrations/
└── xxxx_create_concepts_table.php

resources/views/concepts/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
└── archived.blade.php

routes/web.php
```

---

## `concepts` Table Schema

```sql
concepts (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(200) NOT NULL,
  explanation   TEXT NOT NULL,
  difficulty    ENUM('junior', 'mid', 'senior') NOT NULL,
  status        ENUM('to_review', 'in_progress', 'mastered') NOT NULL DEFAULT 'to_review',
  domain_id     BIGINT UNSIGNED NOT NULL,
  deleted_at    TIMESTAMP NULL,
  created_at    TIMESTAMP,
  updated_at    TIMESTAMP,
  FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE
)
```

---

## Expected Concept Model

```php
class Concept extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'explanation', 'difficulty', 'status', 'domain_id'];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function generatedQuestions(): HasMany
    {
        return $this->hasMany(GeneratedQuestion::class);
    }

    // Laravel 9+ Accessor syntax
    public function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'to_review'   => 'To Review',
                'in_progress' => 'In Progress',
                'mastered'    => 'Mastered',
                default       => $this->status,
            }
        );
    }

    public function difficultyLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->difficulty) {
                'junior' => 'Junior',
                'mid'    => 'Mid',
                'senior' => 'Senior',
                default  => $this->difficulty,
            }
        );
    }
}
```

---

## Quick Status Change Logic (US9)

```php
// In ConceptController
public function updateStatus(Concept $concept): RedirectResponse
{
    $next = match($concept->status) {
        'to_review'   => 'in_progress',
        'in_progress' => 'mastered',
        'mastered'    => 'to_review',
    };

    $concept->update(['status' => $next]);

    return back()->with('success', 'Status updated.');
}
```

Route to add in `web.php`:
```php
Route::patch('/concepts/{concept}/status', [ConceptController::class, 'updateStatus'])
    ->name('concepts.updateStatus');
```

---

## Combined Filter Logic (US5 + Bonus)

```php
// In ConceptController@index
public function index(Domain $domain, Request $request): View
{
    $query = $domain->concepts();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('difficulty')) {
        $query->where('difficulty', $request->difficulty);
    }

    $concepts = $query->get();

    return view('concepts.index', compact('domain', 'concepts'));
}
```

---

## Validation (StoreConceptRequest)

```php
public function rules(): array
{
    return [
        'title'       => ['required', 'string', 'max:200'],
        'explanation' => ['required', 'string'],
        'difficulty'  => ['required', 'in:junior,mid,senior'],
    ];
}
```

---

## Security: Data Isolation

In `ConceptController`, before any operation on a concept, verify that the parent domain belongs to the authenticated user:

```php
// Using nested resourceful routes
Route::resource('domains.concepts', ConceptController::class);

// Or manually in each method:
$domain = auth()->user()->domains()->findOrFail($domainId);
```

---

## Plan Prompt Sent to Agent (OpenCode — base CRUD)

```
Prompt sent to OpenCode in Plan mode:

"In my Laravel 13 app, I want to create the full CRUD for a Concept model.
A concept belongs to a Domain. It has: title (string), explanation (text),
difficulty (enum: junior/mid/senior), status (enum: to_review/in_progress/mastered).
I want: SoftDeletes, two Accessors (statusLabel, difficultyLabel) using
Attribute::make() syntax, and a GET filter by status AND difficulty that can
be combined. Status is not selectable on creation.
List all files, controller methods, and the migration. Do not generate code."
```

**Plan output:** Correct on migration and CRUD structure. The agent used the old `get...Attribute()` syntax in its plan — clarified "use Attribute::make()" and it was corrected. ✅

---

## Plan Prompt Sent to Agent (Claude Code — quick status change)

```
Prompt sent to Claude Code in Plan mode:

"I have a ConceptController in Laravel 13. I want to add a
PATCH /concepts/{concept}/status action that cycles the status
in this order: to_review → in_progress → mastered → to_review.
Show me how to add the route, the controller method,
and the Blade form in the concept list. Do not generate code."
```

**Plan output:** The agent correctly proposed `match()` for the cycle. It forgot the `@csrf` directive in the Blade form — added manually. ✅

---

## Manual Tests Checklist

- [ ] Create a concept → status initialized to "To Review"
- [ ] Accessors: "to_review" → "To Review", "junior" → "Junior" in views
- [ ] Filter by status alone → list filtered correctly
- [ ] Filter by difficulty alone → list filtered correctly
- [ ] Combined filter status + difficulty → works correctly
- [ ] Quick status change: to_review → in_progress → mastered → to_review
- [ ] Delete a concept → soft delete (no longer appears in list)
- [ ] Archived page → the deleted concept appears
- [ ] Restore → the concept reappears in the list
- [ ] Attempt to access another user's concept → 403 or 404
- [ ] Debugbar: no N+1 on index page (concepts loaded with `with('domain')`)