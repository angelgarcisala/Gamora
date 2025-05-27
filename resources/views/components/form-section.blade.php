@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-4 py-5 sm:p-6 
                        bg-white/10 backdrop-blur-md rounded-tl-2xl rounded-tr-2xl shadow-xl">
                <div class="grid grid-cols-6 gap-6">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end px-4 py-3 
                            bg-white/10 rounded-bl-2xl rounded-br-2xl shadow-xl">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
