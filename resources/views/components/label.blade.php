@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#FFFFFF]']) }}>
    {{ $value ?? $slot }}
</label>
