@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4 bg-purple-900 border-b border-purple-700 rounded-t-2xl">
        <div class="text-2xl font-semibold text-white">
            {{ $title }}
        </div>

        <div class="mt-4 text-sm text-purple-200">
            {{ $content }}
        </div>
    </div>

    <div class="flex justify-end space-x-2 px-6 py-4 bg-purple-800 border-t border-purple-700 rounded-b-2xl">
        {{ $footer }}
    </div>
</x-modal>
