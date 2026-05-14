<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Domains') }}
            </h2>
            <a href="{{ route('domains.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                + New Domain
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

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($domains as $domain)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="inline-block w-4 h-4 rounded-full {{ $domain->color }}"></span>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $domain->name }}
                                    </h3>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $domain->mastered_count }}/{{ $domain->concepts_count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all"
                                         style="width: {{ $domain->concepts_count > 0 ? ($domain->mastered_count / $domain->concepts_count) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('domains.show', $domain) }}"
                                   class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                                    View Concepts
                                </a>
                                <div class="flex gap-2">
                                    <a href="{{ route('domains.edit', $domain) }}"
                                       class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                        Edit
                                    </a>
                                    <form action="{{ route('domains.destroy', $domain) }}" method="POST"
                                          onsubmit="return confirm('Delete this domain and all its concepts?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-sm text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                        No domains yet.
                        <a href="{{ route('domains.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline block mt-2">
                            Create your first domain
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
