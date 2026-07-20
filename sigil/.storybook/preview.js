import '../src/web.css';

/* Theme toolbar mirrors Catalyst: it stamps data-theme on <html>, exactly like
 * theme_controller.js, so stories exercise the same light/dark token swap. */
export const globalTypes = {
    theme: {
        description: 'Light / dark theme',
        defaultValue: 'dark',
        toolbar: {
            title: 'Theme',
            icon: 'contrast',
            items: [
                { value: 'light', title: 'Light', icon: 'sun' },
                { value: 'dark', title: 'Dark (void)', icon: 'moon' },
            ],
            dynamicTitle: true,
        },
    },
};

const withTheme = (story, context) => {
    const theme = context.globals.theme ?? 'dark';
    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;
    document.body.style.background = 'var(--color-bg)';
    document.body.style.color = 'var(--color-text)';
    return story();
};

export const decorators = [withTheme];

export const parameters = {
    layout: 'padded',
    controls: { matchers: { color: /(color|background)$/i, date: /Date$/i } },
    backgrounds: { disable: true },
    a11y: { context: '#storybook-root' },
};
