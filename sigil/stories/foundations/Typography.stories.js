import { el } from '../_helpers.js';

export default {
    title: 'Foundations/Typography',
    parameters: { layout: 'padded' },
};

const row = (label, sampleStyle, text) => {
    const r = el('div');
    Object.assign(r.style, { display: 'grid', gridTemplateColumns: '180px 1fr', gap: '20px', alignItems: 'baseline', padding: '10px 0', borderBottom: '1px solid var(--color-border)' });
    const l = el('div', 'u-eyebrow', label);
    const s = el('div', null, text);
    Object.assign(s.style, sampleStyle);
    r.append(l, s);
    return r;
};

export const Families = () => {
    const wrap = el('div');
    wrap.appendChild(row('display · Cinzel', { fontFamily: 'var(--font-display)', fontSize: '28px', letterSpacing: '0.06em' }, 'Aubaine — The Great Codex'));
    wrap.appendChild(row('body · Spectral', { fontFamily: 'var(--font-body)', fontSize: '17px' }, 'The story leads and the rules follow.'));
    wrap.appendChild(row('ui · Barlow Semi Cond.', { fontFamily: 'var(--font-ui)', fontSize: '15px', fontWeight: 600 }, 'Add page · Duplicate · Delete'));
    wrap.appendChild(row('garamond · EB Garamond', { fontFamily: 'var(--font-garamond)', fontSize: '17px', fontStyle: 'italic' }, 'Déchaînez votre imagination.'));
    wrap.appendChild(row('mono · JetBrains Mono', { fontFamily: 'var(--font-mono)', fontSize: '13px', letterSpacing: '0.15em', textTransform: 'uppercase' }, 'Skill · Tier II · 10 XP'));
    return wrap;
};

export const Scale = () => {
    const sizes = ['--text-xs', '--text-sm', '--text-base', '--text-lg', '--text-xl', '--text-2xl', '--text-3xl'];
    const wrap = el('div');
    sizes.forEach((t) => {
        const line = el('div', null, `${t} — Aubaine`);
        Object.assign(line.style, { fontFamily: 'var(--font-display)', fontSize: `var(${t})`, margin: '8px 0' });
        wrap.appendChild(line);
    });
    return wrap;
};

export const Gilt = () => {
    const h = el('h1', 'u-gilt', 'Parangon');
    Object.assign(h.style, { fontFamily: 'var(--font-epic)', fontSize: '68px', fontVariant: 'small-caps', margin: 0 });
    const wrap = el('div');
    wrap.style.padding = '24px';
    wrap.style.background = 'var(--void-950)';
    wrap.appendChild(h);
    return wrap;
};
