import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // CRITICAL: Safelist ensures dynamic status badge classes are never purged
    safelist: [
        // Status badge background colors
        'bg-yellow-100',
        'bg-blue-100',
        'bg-green-100',
        'bg-red-100',
        'bg-gray-100',
        'bg-gray-900',
        'bg-orange-100',

        // Status badge text colors
        'text-yellow-800',
        'text-blue-800',
        'text-green-800',
        'text-red-800',
        'text-gray-800',
        'text-white',
        'text-orange-800',

        // Status badge border colors
        'border-yellow-300',
        'border-blue-300',
        'border-green-300',
        'border-red-300',
        'border-gray-300',
        'border-gray-900',
        'border-orange-300',

        // Utility classes
        'inline-flex',
        'items-center',
        'rounded-full',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#E6F2FF',
                    100: '#CCE5FF',
                    200: '#99CBFF',
                    300: '#66B0FF',
                    400: '#3396FF',
                    500: '#0066CC',
                    600: '#0052A3',
                    700: '#003D7A',
                    800: '#002952',
                    900: '#001429',
                },
                secondary: {
                    50: '#E6F9F2',
                    100: '#CCF3E5',
                    200: '#99E7CB',
                    300: '#66DBB1',
                    400: '#33CF97',
                    500: '#00A86B',
                    600: '#008C59',
                    700: '#007047',
                    800: '#005435',
                    900: '#003823',
                },
                accent: '#FF6B35',
                background: '#F8F9FA',
                surface: '#FFFFFF',
            },
        },
    },

    plugins: [
        forms,
        daisyui,
    ],

    daisyui: {
        themes: [
            {
                kylehms: {
                    "primary": "#0066CC",
                    "secondary": "#00A86B",
                    "accent": "#FF6B35",
                    "neutral": "#2D3748",
                    "base-100": "#FFFFFF",
                    "info": "#3B82F6",
                    "success": "#10B981",
                    "warning": "#F59E0B",
                    "error": "#EF4444",
                },
            },
            "light",
            "dark",
        ],
        base: true,
        styled: true,
        utils: true,
    },
};
