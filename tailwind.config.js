/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './assets/**/*.{js,ts,jsx,tsx,css}',
        './templates/**/*.twig',
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
