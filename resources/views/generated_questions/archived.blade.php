<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Archived Generated Questions') }}
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
                @forelse($archived as $generation)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg opacity-75">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Concept: <strong>{{ $generation->concept->title ?? 'Deleted' }}</strong>
                                    </span>
                                    <br>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Domain:
                                        <span class="inline-flex items-center gap-1">
                                            @if($generation->concept->domain ?? false)
                                                <span class="inline-block w-2 h-2 rounded-full {{ $generation->concept->domain->color }}"></span>
                                                {{ $generation->concept->domain->name }}
                                            @else
                                                Deleted
                                            @endif
                                        </span>
                                    </span>
                                    <br>
                                    <span class="text-xs text-gray-400">
                                        Generated {{ $generation->created_at->format('m/d/Y \a\t H:i') }}
                                    </span>
                                </div>
                                <form action="{{ route('generatedQuestions.destroy', $generation) }}" method="POST"
                                      onsubmit="return confirm('Permanently delete this generation?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1 text-sm border border-red-300 dark:border-red-600 rounded-md text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900">
                                        Delete Forever
                                    </button>
                                </form>
                            </div>
                            <ol class="list-decimal list-inside space-y-1">
                                @foreach($generation->questions as $question)
                                    <li class="text-gray-700 dark:text-gray-300">{{ $question }}</li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        No archived generated questions.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
