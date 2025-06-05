<x-form-section submit="updatePassword">
    <x-slot name="title">
        <span class="text-[#FFFFFF]">
            {{ __('Actualizar contraseña') }}
        </span>
    </x-slot>

    <x-slot name="description">
        <p class="text-[#FFFACD]">
            {{ __('Asegúrate de que tu cuenta use una contraseña larga y aleatoria para mantenerla segura.') }}
        </p>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Contraseña actual') }}" class="text-[#FFFFFF]" />
            <x-input
                id="current_password"
                type="password"
                class="mt-1 block w-full bg-white/10 text-white placeholder-white/60 border border-white/20 rounded-md focus:bg-white/20 focus:border-white focus:ring-white"
                wire:model="state.current_password"
                autocomplete="current-password"
            />
            <x-input-error for="current_password" class="mt-2 text-[#FFFACD]" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('Nueva contraseña') }}" class="text-[#FFFFFF]" />
            <x-input
                id="password"
                type="password"
                class="mt-1 block w-full bg-white/10 text-white placeholder-white/60 border border-white/20 rounded-md focus:bg-white/20 focus:border-white focus:ring-white"
                wire:model="state.password"
                autocomplete="new-password"
            />
            <x-input-error for="password" class="mt-2 text-[#FFFACD]" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirmar contraseña') }}" class="text-[#FFFFFF]" />
            <x-input
                id="password_confirmation"
                type="password"
                class="mt-1 block w-full bg-white/10 text-white placeholder-white/60 border border-white/20 rounded-md focus:bg-white/20 focus:border-white focus:ring-white"
                wire:model="state.password_confirmation"
                autocomplete="new-password"
            />
            <x-input-error for="password_confirmation" class="mt-2 text-[#FFFACD]" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3 text-[#FFFFFF]" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button class="bg-purple-800 hover:bg-purple-900 text-[#FFFFFF]">
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>
