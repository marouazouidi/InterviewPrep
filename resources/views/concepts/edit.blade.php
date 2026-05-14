<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Concept') }}
            </h2>
            <a href="{{ route('domains.show', $concept->domain) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Back to {{ $concept->domain->name }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span>Domain:</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="inline-block w-3 h-3 rounded-full {{ $concept->domain->color }}"></span>
                            {{ $concept->domain->name }}
                        </span>
                    </div>

                    <form action="{{ route('concepts.update', $concept) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $concept->title)" required maxlength="200" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="explanation" :value="__('Explanation')" />
                            <textarea id="explanation" name="explanation" rows="8"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                      required>{{ old('explanation', $concept->explanation) }}</textarea>
                            <x-input-error :messages="$errors->get('explanation')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="difficulty" :value="__('Difficulty')" />
                            <select id="difficulty" name="difficulty" required
                                    class="mt-1 block rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="junior" {{ old('difficulty', $concept->difficulty) === 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="mid" {{ old('difficulty', $concept->difficulty) === 'mid' ? 'selected' : '' }}>Mid</option>
                                <option value="senior" {{ old('difficulty', $concept->difficulty) === 'senior' ? 'selected' : '' }}>Senior</option>
                            </select>
                            <x-input-error :messages="$errors->get('difficulty')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" required
                                    class="mt-1 block rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="to_review" {{ old('status', $concept->status) === 'to_review' ? 'selected' : '' }}>To Review</option>
                                <option value="in_progress" {{ old('status', $concept->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="mastered" {{ old('status', $concept->status) === 'mastered' ? 'selected' : '' }}>Mastered</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
                            <a href="{{ route('domains.show', $concept->domain) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
