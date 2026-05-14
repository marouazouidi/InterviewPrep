<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <span class="inline-block w-4 h-4 rounded-full {{ $domain->color }}"></span>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $domain->name }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('domains.edit', $domain) }}"
                   class="inline-flex items-center px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Edit
                </a>
                <a href="{{ route('concepts.create', $domain) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    + New Concept
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('domains.show', $domain) }}" class="flex gap-4 items-end">
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status"
                                    class="mt-1 block rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">All</option>
                                <option value="to_review" {{ request('status') === 'to_review' ? 'selected' : '' }}>To Review</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="mastered" {{ request('status') === 'mastered' ? 'selected' : '' }}>Mastered</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="difficulty" :value="__('Difficulty')" />
                            <select id="difficulty" name="difficulty"
                                    class="mt-1 block rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">All</option>
                                <option value="junior" {{ request('difficulty') === 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="mid" {{ request('difficulty') === 'mid' ? 'selected' : '' }}>Mid</option>
                                <option value="senior" {{ request('difficulty') === 'senior' ? 'selected' : '' }}>Senior</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <x-primary-button>{{ __('Filter') }}</x-primary-button>
                            @if(request('status') || request('difficulty'))
                                <a href="{{ route('domains.show', $domain) }}"
                                   class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($concepts as $concept)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <a href="{{ route('concepts.show', $concept) }}"
                                       class="text-lg font-semibold text-gray-900 dark:text-gray-100 hover:underline">
                                        {{ $concept->title }}
                                    </a>
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded
                                        {{ $concept->difficulty === 'junior' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $concept->difficulty === 'mid' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $concept->difficulty === 'senior' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                        {{ $concept->difficultyLabel }}
                                    </span>
                                </div>

                                <form action="{{ route('concepts.status', $concept) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 text-sm
                                                {{ $concept->status === 'to_review' ? 'text-gray-400' : '' }}
                                                {{ $concept->status === 'in_progress' ? 'text-blue-600 dark:text-blue-400' : '' }}
                                                {{ $concept->status === 'mastered' ? 'text-green-600 dark:text-green-400' : '' }}
                                                hover:underline">
                                        <span class="inline-block w-2 h-2 rounded-full
                                            {{ $concept->status === 'to_review' ? 'bg-gray-400' : '' }}
                                            {{ $concept->status === 'in_progress' ? 'bg-blue-500' : '' }}
                                            {{ $concept->status === 'mastered' ? 'bg-green-500' : '' }}">
                                        </span>
                                        {{ $concept->statusLabel }}
                                    </button>
                                </form>
                            </div>

                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('concepts.show', $concept) }}"
                                   class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View</a>
                                <a href="{{ route('concepts.edit', $concept) }}"
                                   class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Edit</a>
                                <form action="{{ route('concepts.archive', $concept) }}" method="POST"
                                      onsubmit="return confirm('Archive this concept?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-sm text-red-600 dark:text-red-400 hover:underline">Archive</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        No concepts in this domain yet.
                        <a href="{{ route('concepts.create', $domain) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline block mt-2">
                            Create your first concept
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
