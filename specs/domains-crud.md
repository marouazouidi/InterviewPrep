# Spec — Domain CRUD

> Feature: Technical domain management
> User Stories covered: **US2, US3, US4**
> Branch: `feature/domains-crud`
> Agent used: OpenCode

---

## Context

A domain is the top-level category that groups technical concepts together. Examples: "Laravel ORM", "PHP OOP", "MySQL", "REST API". Each user creates their own domains. A domain has a badge color to identify it visually in the list.

The domain list is the main page after the dashboard — it is the entry point to all concepts.

---

## User Stories Covered

### US2 — Domain List
The user sees all their domains with:
- Domain name
- Color-coded badge
- Total number of concepts in that domain
- Number of concepts with "mastered" status
- A link to the domain's concepts

### US3 — Create a Domain
Form with:
- `name` (text, required, max 100 characters)
- `color` (badge color, from a predefined list: blue, green, red, orange, purple, pink)

### US4 — Edit / Delete a Domain
- Edit the name and/or color
- Delete the domain (cascade delete on all associated concepts)

---

## What I WANT

- `DomainController` with methods: `index`, `create`, `store`, `edit`, `update`, `destroy`
- Resourceful route `Route::resource('domains', DomainController::class)` with `auth` middleware
- Form Requests: `StoreDomainRequest` and `UpdateDomainRequest` for validation
- `Domain` model with `belongsTo(User::class)` and `hasMany(Concept::class)` relationships
- In `index()`, use `auth()->user()->domains()->withCount(['concepts', 'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered')])->get()`
- Color stored as a `string` — store the TailwindCSS class directly (e.g. `bg-blue-500`)
- Deleting a domain must cascade-delete its concepts (FK constraint `onDelete('cascade')`)
- Blade views in `resources/views/domains/`: `index.blade.php`, `create.blade.php`, `edit.blade.php`
- Each domain in the list shows a simple progress bar (mastered concepts / total)

---

## What I DO NOT WANT

- No `SoftDeletes` on domains (only on concepts)
- No pagination on the list (number of domains stays manageable)
- No HTML `<input type="color">` — use radio buttons with predefined colors for better UX
- No dynamic color logic in PHP — store the Tailwind class directly in the database
- No access to other users' domains — always filter by `auth()->id()`

---

## Expected File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── DomainController.php
│   └── Requests/
│       ├── StoreDomainRequest.php
│       └── UpdateDomainRequest.php
├── Models/
│   └── Domain.php

database/migrations/
└── xxxx_create_domains_table.php

resources/views/domains/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

routes/web.php  (add resource route)
```

---

## `domains` Table Schema

```sql
domains (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  color         VARCHAR(50) NOT NULL DEFAULT 'bg-blue-500',
  user_id       BIGINT UNSIGNED NOT NULL,
  created_at    TIMESTAMP,
  updated_at    TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)
```

---

## Expected Domain Model

```php
class Domain extends Model
{
    protected $fillable = ['name', 'color', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function concepts(): HasMany
    {
        return $this->hasMany(Concept::class);
    }
}
```

---

## Expected Validation (StoreDomainRequest)

```php
public function rules(): array
{
    return [
        'name'  => ['required', 'string', 'max:100'],
        'color' => ['required', 'string', 'in:bg-blue-500,bg-green-500,bg-red-500,bg-orange-500,bg-purple-500,bg-pink-500'],
    ];
}

public function authorize(): bool
{
    return true; // Auth already handled by middleware
}
```

---

## Available Badge Colors

| Display Name | Tailwind Class |
|---|---|
| Blue | `bg-blue-500` |
| Green | `bg-green-500` |
| Red | `bg-red-500` |
| Orange | `bg-orange-500` |
| Purple | `bg-purple-500` |
| Pink | `bg-pink-500` |

---

## Security: User Data Isolation

In `DomainController`, **always** retrieve domains via `auth()->user()->domains()` and never via `Domain::all()`.

For `show`, `edit`, `update`, `destroy`: use `auth()->user()->domains()->findOrFail($id)` to prevent a user from accessing another user's domains.

---

## Plan Prompt Sent to Agent

```
Prompt sent to OpenCode in Plan mode:

"I'm building a Laravel 13 app. I want to create the full CRUD
for a Domain model (fields: name, color, user_id).
Each user can only see their own domains.
The list must show the total number of concepts per domain
and the number of concepts with 'mastered' status.
List all files to create, their methods,
and explain how to avoid N+1 problems.
Do not generate any code."
```

**Plan output:** The agent correctly identified `withCount()` with a conditional sub-query for mastered concepts. It listed both Form Requests, all 3 Blade views, and the migration with cascade FK. ✅

**What changed after specifying "What I DO NOT WANT":** The agent had initially proposed `Domain::where('user_id', auth()->id())` instead of `auth()->user()->domains()`. After clarification, it used the correct Eloquent relationship. ✅

---

## Manual Tests Checklist

- [ ] Create a domain → appears in the list with the correct color badge
- [ ] Counters show "0/0" for a new domain with no concepts
- [ ] Edit name and color → updated correctly
- [ ] Delete a domain that has concepts → concepts are cascade-deleted
- [ ] Attempt to access another user's domain → 404
- [ ] Debugbar: no N+1 query on the index page