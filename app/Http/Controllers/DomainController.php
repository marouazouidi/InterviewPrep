<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Models\Domain;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;


class DomainController extends Controller
{
    use AuthorizesRequests;
    public function index(): View
    {
        $this->authorize('viewAny', Domain::class);
        
        $domains =  Auth::user()
            ->domains()
            ->withCount([
                'concepts',
                'concepts as mastered_count' => fn ($q) => $q->where('status', 'mastered'),
            ])
            ->latest()
            ->get();


        return view('domains.index', compact('domains'));
    }

    public function create(): View
    {
        $this->authorize('create', Domain::class);
        return view('domains.create');
    }

    public function store(StoreDomainRequest $request)
    {
        $this->authorize('create', Domain::class);
        
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        Domain::create($data);

        return redirect()->route('domains.index')->with('success', 'Domain created.');
    }

    public function show(Domain $domain, Request $request): View
    {
        $this->authorize('view', $domain);

        $query = $domain->concepts();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $concepts = $query->latest()->get();

        return view('domains.show', compact('domain', 'concepts'));
}

    public function edit(Domain $domain)
    {
        $this->authorize('update', $domain);

        return view('domains.edit', compact('domain'));
    }

    public function update(UpdateDomainRequest $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $domain->update($request->validated());

        return redirect()->route('domains.show', $domain)->with('success', 'Domain updated.');
    }

    public function destroy(Domain $domain)
    {
        $this->authorize('delete', $domain);

        $domain->delete();

        return redirect()->route('domains.index')->with('success', 'Domain deleted.');
    }
}
