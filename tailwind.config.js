/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './assets/**/*.{js,ts,jsx,tsx,css}',
        './templates/**/*.twig',
    ],
    safelist: [
        'bg-emerald-50', 'border-emerald-900', 'text-emerald-950', 
        'bg-red-50', 'border-red-900', 'text-red-950',
        'bg-yellow-50', 'border-yellow-900', 'text-yellow-950',
        'bg-indigo-50', 'border-indigo-900', 'text-indigo-950',
        'bg-orange-50', 'border-orange-900', 'text-orange-950',
        'bg-stone-50', 'border-stone-900', 'text-stone-950',
        'bg-purple-50', 'border-purple-900', 'text-purple-950',
        'bg-pink-50', 'border-pink-900', 'text-pink-950',
        'border-emerald-900', 'border-red-900', 'border-yellow-900', 'border-indigo-900', 'border-orange-900', 'border-stone-900', 'border-purple-900', 'border-pink-900',
        'text-emerald-900', 'text-red-900', 'text-yellow-900', 'text-indigo-900', 'text-orange-900', 'text-stone-900', 'text-purple-900', 'text-pink-900',
        
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('daisyui'),
    ],
    daisyui: {
        themes: ['emerald', 'light', 'dark'],
    },
};
