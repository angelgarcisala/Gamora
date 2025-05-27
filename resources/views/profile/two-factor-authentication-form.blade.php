<x-action-section>
    <x-slot name="title">
        {{ __('Autenticación de dos factores') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Agrega seguridad adicional a tu cuenta usando la autenticación de dos factores.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-[#FFFFFF]">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ __('Termina de habilitar la autenticación de dos factores.') }}
                @else
                    {{ __('Has habilitado la autenticación de dos factores.') }}
                @endif
            @else
                {{ __('No has habilitado la autenticación de dos factores.') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-[#FFFACD]">
            <p>
                {{ __('Cuando la autenticación de dos factores esté habilitada, se te pedirá un token seguro y aleatorio durante la autenticación. Puedes obtener este token de la aplicación Google Authenticator de tu teléfono.') }}
            </p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <!-- QR Code Instructions -->
                <div class="mt-4 max-w-xl text-sm text-[#FFFACD]">
                    <p class="font-semibold">
                        @if ($showingConfirmation)
                            {{ __('Para terminar de habilitar la autenticación de dos factores, escanea el siguiente código QR con la aplicación de autenticación de tu teléfono o introduce la clave de configuración y proporciona el código OTP generado.') }}
                        @else
                            {{ __('La autenticación de dos factores ahora está habilitada. Escanea el siguiente código QR con la aplicación de autenticación de tu teléfono o introduce la clave de configuración.') }}
                        @endif
                    </p>
                </div>

                <div class="mt-4 p-2 inline-block bg-purple-800">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-4 max-w-xl text-sm text-[#FFFACD]">
                    <p class="font-semibold">
                        {{ __('Clave de configuración') }}: {{ decrypt($this->user->two_factor_secret) }}
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4">
                        <x-label for="code" value="{{ __('Código') }}" />

                        <x-input
                            id="code"
                            type="text"
                            name="code"
                            class="block mt-1 w-1/2"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication"
                        />

                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <!-- Recovery Codes -->
                <div class="mt-4 max-w-xl text-sm text-[#FFFACD]">
                    <p class="font-semibold">
                        {{ __('Guarda estos códigos de recuperación en un gestor de contraseñas seguro. Pueden usarse para recuperar el acceso a tu cuenta si tu dispositivo de autenticación de dos factores se pierde.') }}
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-purple-700 rounded-lg text-[#FFFACD]">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button type="button" wire:loading.attr="disabled" class="bg-purple-800 hover:bg-purple-900">
                        {{ __('Habilitar') }}
                    </x-button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-secondary-button class="me-3">
                            {{ __('Regenerar códigos de recuperación') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <x-button type="button" class="me-3" wire:loading.attr="disabled">
                            {{ __('Confirmar') }}
                        </x-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <x-secondary-button class="me-3">
                            {{ __('Mostrar códigos de recuperación') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-secondary-button wire:loading.attr="disabled">
                            {{ __('Cancelar') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-danger-button wire:loading.attr="disabled">
                            {{ __('Deshabilitar') }}
                        </x-danger-button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
    </x-slot>
</x-action-section>
