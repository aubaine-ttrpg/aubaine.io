/*
 * Framework-agnostic HTML renderer: stories are plain DOM/strings styled by the
 * shared CSS, so the same catalog documents the CSS that Catalyst (Twig) and
 * Almanach (Astro) both consume.
 */
const config = {
    stories: ['../stories/**/*.stories.@(js|mjs)'],
    // Storybook 10 folded controls/actions/viewport/backgrounds/toolbars into core;
    // docs and a11y remain opt-in addons.
    addons: ['@storybook/addon-docs', '@storybook/addon-a11y'],
    framework: { name: '@storybook/html-vite', options: {} },
    // fixtures/ is served at the web root (print stories fetch /print/*.html and
    // /design-data.json); Catalyst's built assets are mounted at /build so the
    // fingerprinted cover art, node icons, and paper textures resolve.
    staticDirs: [
        '../fixtures',
        { from: '../../catalyst/public/build', to: '/build' },
    ],
    // Resolve @aubaine/sigil to our own src so the print stylesheet (which imports
    // the shared tokens by package name) resolves inside Storybook too.
    async viteFinal(config) {
        const { mergeConfig } = await import('vite');
        const path = await import('node:path');
        // Storybook 10 loads this config as ESM, so use import.meta.dirname (Node 20.11+).
        return mergeConfig(config, {
            resolve: {
                alias: {
                    '@aubaine/sigil': path.resolve(import.meta.dirname, '../src'),
                },
            },
        });
    },
};

export default config;
