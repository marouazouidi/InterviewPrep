<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Archived Concepts') }}
            </h2>
            <a href="{{ route('domains.index') }}"
               class="inline-flex items-center px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                &larr; Back to Domains
            </a>
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

            <div class="space-y-4">
                @forelse($concepts as $concept)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg opacity-75">
                        <div class="p-6 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $concept->title }}
                                    </h3>
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded
                                        {{ $concept->difficulty === 'junior' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $concept->difficulty === 'mid' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $concept->difficulty === 'senior' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $concept->difficultyLabel }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="inline-block w-2 h-2 rounded-full {{ $concept->domain->color }}"></span>
                                        {{ $concept->domain->name }}
                                    </span>
                                    <span>&middot;</span>
                                    <span>Deleted {{ $concept->deleted_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <form action="{{ route('concepts.restore', $concept) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        Restore
                                    </button>
                                </form>
                                <form action="{{ route('concepts.forceDelete', $concept) }}" method="POST"
                                      onsubmit="return confirm('Permanently delete this concept? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1 text-sm border border-red-300 dark:border-red-600 rounded-md text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900">
                                        Delete Forever
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        No archived concepts.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
