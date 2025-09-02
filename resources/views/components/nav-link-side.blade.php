@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'flex items-center px-6 py-2 mt-4 text-gray-100 bg-dishub-blue-900 bg-opacity-50'
            : 'flex items-center px-6 py-2 mt-4 text-gray-400 hover:bg-dishub-blue-700 hover:bg-opacity-50 hover:text-gray-100 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
