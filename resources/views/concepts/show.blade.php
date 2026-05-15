<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('domains.show', $concept->domain) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    &larr; {{ $concept->domain->name }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-1">
                    {{ $concept->title }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('concepts.edit', $concept) }}"
                   class="inline-flex items-center px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Edit
                </a>
                <form action="{{ route('concepts.archive', $concept) }}" method="POST"
                      onsubmit="return confirm('Archive this concept?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-1 text-sm border border-red-300 dark:border-red-600 rounded-md text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900">
                        Archive
                    </button>
                </form>
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
                    <div class="flex gap-2 mb-4">
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded
                            {{ $concept->difficulty === 'junior' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $concept->difficulty === 'mid' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $concept->difficulty === 'senior' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                            {{ $concept->difficultyLabel }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded
                            {{ $concept->status === 'to_review' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}
                            {{ $concept->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $concept->status === 'mastered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}">
                            {{ $concept->statusLabel }}
                        </span>
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $concept->explanation }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Generate Interview Questions
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Generate 5 technical interview questions based on your explanation using AI.
                    </p>
                    <form action="{{ route('generatedQuestions.store', $concept) }}" method="POST">
                        @csrf
                        <x-primary-button>{{ __('Generate Interview Questions') }}</x-primary-button>
                    </form>
                </div>
            </div>

            @if($concept->relationLoaded('generatedQuestions') && $concept->generatedQuestions->isNotEmpty())
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Generation History
                    </h3>

                    @foreach($concept->generatedQuestions as $generation)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Generated on {{ $generation->created_at->format('m/d/Y \a\t H:i') }}
                                    </span>
                                    <form action="{{ route('generatedQuestions.destroy', $generation) }}" method="POST"
                                          onsubmit="return confirm('Delete this generation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-sm text-red-600 dark:text-red-400 hover:underline">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                                @php $questions = $generation->questions ?? []; @endphp
                                @if(is_array($questions) && count($questions) > 0)
                                    <ol class="list-decimal list-inside space-y-2">
                                        {{-- @dd($questions) --}}
                                        @foreach($questions as $question)
                                            <li class="text-gray-700 dark:text-gray-300">{{ $question }}</li>
                                        @endforeach
                                    </ol>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
