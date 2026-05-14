<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Domain') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('domains.store') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required maxlength="100" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label :value="__('Color')" />
                            <div class="mt-2 grid grid-cols-6 gap-3">
                                @foreach(['bg-blue-500', 'bg-green-500', 'bg-red-500', 'bg-orange-500', 'bg-purple-500', 'bg-pink-500'] as $color)
                                    <label class="flex flex-col items-center gap-1 cursor-pointer">
                                        <input type="radio" name="color" value="{{ $color }}"
                                               class="sr-only peer" {{ old('color', 'bg-blue-500') === $color ? 'checked' : '' }} required>
                                        <span class="inline-block w-8 h-8 rounded-full {{ $color }} ring-2 ring-transparent peer-checked:ring-indigo-500 peer-checked:ring-offset-2 transition-all"></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ explode('-', $color)[1] }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('color')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create') }}</x-primary-button>
                            <a href="{{ route('domains.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
