# AGENTS.md — InterviewPrep

> Reference file for the use of coding agents in this project.
> Committed on Day 1 — every feature built with an agent is documented here.

---

## 🧠 Project

**InterviewPrep** — Laravel application for preparing technical interviews.
Stack: Laravel 11, MySQL, Blade, Groq API (via `Http::` facade).
Duration: 5 days | Start: 11/05/2026 | Deadline: 15/05/2026 13:00

---

## 🤖 Coding Agent Used

**OpenCode** (`opencode.ai`)
Terminal-based, open-source, free — used in **Plan → Build** mode for every feature.

> Standard workflow:
>
> 1. Describe the feature + constraints in a `specs/<feature>.md` file
> 2. Run OpenCode in **Plan** mode → review, validate, and adjust the plan
> 3. Run OpenCode in **Build** mode → generate the code
> 4. Manual review → apply adjustments if necessary
> 5. Commit with explicit AI mention

---

## 🔌 AI API Used

**Groq API** — `console.groq.com`
Model: `llama3-8b-8192` (or `mixtral-8x7b-32768`)
Request method: Laravel `Http::` facade — no external package
API key stored only in `.env` → `GROQ_API_KEY`

---

## 📁 Specs Structure

Each feature built with a coding agent has its own file inside `specs/`:

```txt
specs/
├── auth.md
├── domains-crud.md
├── concepts-crud.md
├── ai-generation.md
└── dashboard.md        # bonus
```

Each `specs/<feature>.md` file contains:

* **Goal**: what the feature should do
* **What I WANT**: precise expected behaviors
* **What I DO NOT WANT**: explicit constraints to guide the agent
* **Agent output**: generated plan before manual edits
* **Manual modifications**: what was changed and why

---

## 📋 Features and AI Usage

| Feature                       | Agent Used | Spec File                | Branch                  |
| ----------------------------- | ---------- | ------------------------ | ----------------------- |
| Authentication                | OpenCode   | `specs/auth.md`          | `feature/auth`          |
| Domains CRUD                  | OpenCode   | `specs/domains-crud.md`  | `feature/domains-crud`  |
| Concepts CRUD                 | OpenCode   | `specs/concepts-crud.md` | `feature/concepts-crud` |
| AI Question Generation (Groq) | OpenCode   | `specs/ai-generation.md` | `feature/ai-generation` |
| Dashboard (bonus)             | OpenCode   | `specs/dashboard.md`     | `feature/dashboard`     |

---

## 📝 Commit Convention

Any commit involving generated or AI-assisted code must explicitly mention AI usage:

```txt
feat(domains): add CRUD with Eloquent relations [AI-assisted: OpenCode]
feat(ai): integrate Groq API for question generation [AI-assisted: OpenCode]
fix(concepts): correct N+1 query with eager loading [AI-reviewed: OpenCode]
refactor(forms): extract FormRequest classes [AI-assisted: OpenCode]
```

---

## ⚠️ Mandatory Rules

* The `GROQ_API_KEY` must **never** appear in the codebase or in any commit
* API calls must be done **only** with Laravel `Http::` facade — no external SDKs
* Every AI request must include **proper error handling** (no blank pages)
* Generated results must be **saved in the database** before display
* Zero N+1 queries — verified with Laravel Debugbar on all listing pages

---

## 🔍 What the Agent Does Well

*(to be updated during the project)*

* Quickly generates migrations and Eloquent models
* Creates structured Form Request classes
* Generates resourceful controller skeletons
* Navigates the existing codebase before modifying files

---

## ⚡ What the Agent Hallucinates / Gets Wrong

*(to be updated during the project)*

* Sometimes generates reversed Eloquent relationships → always verify `belongsTo` vs `hasMany`
* May forget `with()` eager loading → manually add it systematically
* Groq prompts should be written manually — the agent tends to generate generic prompts

---

## 👤 Author

**Abderrahmane Merradou**
DWWM Training Program — Class of 2026
Individual project — 5 days
