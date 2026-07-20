import { el, grid, readVar, swatch, section, fetchJson } from '../_helpers.js';

export default {
    title: 'Foundations/Colours',
    parameters: { layout: 'padded' },
};

function ramp(title, tokens) {
    const wrap = el('div');
    wrap.appendChild(section(title));
    wrap.appendChild(
        grid(tokens.map(([name, note]) => swatch(`var(${name})`, name, note || readVar(name))), '150px'),
    );
    return wrap;
}

export const Gold = () =>
    ramp('Gold — brand primary + metallic ramp', [
        ['--gold', 'brand primary'],
        ['--gold-metallic-hi'],
        ['--gold-metallic-1'],
        ['--gold-metallic-2'],
        ['--gold-metallic-lo'],
        ['--gold-muted'],
        ['--gold-antique'],
        ['--gold-edge'],
    ]);

export const Void = () =>
    ramp('Void / purple — logos, dark theme, intro', [
        ['--void-100'],
        ['--void-200'],
        ['--void-300'],
        ['--void-400', 'brand void'],
        ['--void-500'],
        ['--void-600'],
        ['--void-700'],
        ['--void-800'],
        ['--void-900'],
        ['--void-950'],
    ]);

export const Semantic = () =>
    ramp('Semantic tokens (swap with the theme toolbar)', [
        ['--color-bg'],
        ['--color-surface'],
        ['--color-surface-2'],
        ['--color-surface-3'],
        ['--color-text'],
        ['--color-text-muted'],
        ['--color-text-faint'],
        ['--color-border'],
        ['--color-accent'],
        ['--color-accent-dim'],
        ['--color-gold'],
        ['--color-danger'],
        ['--color-success'],
    ]);

/** Game-data colours are owned by the PHP enums; shown here from design-data.json. */
export const GameData = () => {
    const root = el('div');
    fetchJson('/design-data.json')
        .then((data) => {
            const block = (title, items, colorKey = 'color') => {
                const b = el('div');
                b.appendChild(section(title));
                b.appendChild(
                    grid(
                        items.map((i) => swatch(i[colorKey], i.labelFr, i.key)),
                        '150px',
                    ),
                );
                return b;
            };
            root.appendChild(block('Domains (src/Design/Domain.php)', data.domains));
            root.appendChild(el('div', null, '<br>'));
            root.appendChild(block('Characteristics (src/Design/Characteristic.php)', data.characteristics));
            root.appendChild(el('div', null, '<br>'));
            root.appendChild(block('Papers (src/Design/Paper.php)', data.papers));
        })
        .catch((e) => {
            root.appendChild(el('p', null, `⚠ ${e.message}`));
        });
    return root;
};
