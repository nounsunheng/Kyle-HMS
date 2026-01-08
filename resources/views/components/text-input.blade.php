@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge([
    'class' => 'border-gray-500 bg-white text-gray-900 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm placeholder-gray-400'
]) }}>
