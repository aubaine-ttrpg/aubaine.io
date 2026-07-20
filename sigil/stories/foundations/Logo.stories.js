import { el } from '../_helpers.js';

export default {
    title: 'Foundations/Logo',
    parameters: { layout: 'centered' },
};

/** Theme-aware mark: gold crest in light, purple void mark in dark. Toggle the theme. */
export const ThemeAware = () => {
    const wrap = el('div');
    Object.assign(wrap.style, { display: 'flex', gap: '32px', alignItems: 'center' });
    const mark = el('span', 'aubaine-logo aubaine-logo--xl');
    mark.setAttribute('role', 'img');
    mark.setAttribute('aria-label', 'Aubaine');
    const lockup = el('div');
    Object.assign(lockup.style, { display: 'flex', flexDirection: 'column', gap: '4px' });
    const name = el('span', 'aubaine-wordmark', 'Aubaine');
    name.style.fontSize = '28px';
    const sub = el('span', 'u-eyebrow', 'The Great Codex');
    lockup.append(name, sub);
    wrap.append(mark, lockup);
    return wrap;
};

export const Sizes = () => {
    const wrap = el('div');
    Object.assign(wrap.style, { display: 'flex', gap: '24px', alignItems: 'center' });
    ['aubaine-logo', 'aubaine-logo aubaine-logo--lg', 'aubaine-logo aubaine-logo--xl'].forEach((cls) => {
        const m = el('span', cls);
        m.setAttribute('role', 'img');
        m.setAttribute('aria-label', 'Aubaine');
        wrap.appendChild(m);
    });
    return wrap;
};

/** All four marks side by side (master, light, void, hexagram). */
export const AllMarks = () => {
    const files = [
        ['aubaine-logo.svg', 'master'],
        ['aubaine-logo-light.svg', 'light (favicon)'],
        ['aubaine-logo-void.svg', 'void (dark)'],
        ['aubaine-logoy.svg', 'hexagram alt'],
    ];
    const wrap = el('div');
    Object.assign(wrap.style, { display: 'flex', gap: '28px', flexWrap: 'wrap' });
    files.forEach(([file, label]) => {
        const cell = el('div');
        Object.assign(cell.style, { display: 'flex', flexDirection: 'column', gap: '8px', alignItems: 'center' });
        const img = el('img');
        img.src = new URL(`../../src/brand/logo/${file}`, import.meta.url).href;
        img.width = 96;
        img.height = 96;
        img.alt = `Aubaine ${label}`;
        cell.append(img, el('div', 'u-eyebrow', label));
        wrap.appendChild(cell);
    });
    return wrap;
};
