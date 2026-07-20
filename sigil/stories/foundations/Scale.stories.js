import { el, readVar } from '../_helpers.js';

export default {
    title: 'Foundations/Scale',
    parameters: { layout: 'padded' },
};

export const Spacing = () => {
    const tokens = ['--space-1', '--space-2', '--space-3', '--space-4', '--space-5', '--space-6', '--space-8', '--space-10', '--space-12'];
    const wrap = el('div');
    tokens.forEach((t) => {
        const row = el('div');
        Object.assign(row.style, { display: 'flex', alignItems: 'center', gap: '16px', margin: '6px 0' });
        const bar = el('div');
        Object.assign(bar.style, { height: '16px', width: `var(${t})`, background: 'var(--color-accent)', borderRadius: 'var(--radius)' });
        const label = el('div', 'u-mono', `${t} · ${readVar(t)}`);
        label.style.fontSize = '12px';
        row.append(bar, label);
        wrap.appendChild(row);
    });
    return wrap;
};

export const Radii = () => {
    const wrap = el('div');
    Object.assign(wrap.style, { display: 'flex', gap: '24px' });
    [['--radius', '2px'], ['--radius-lg', '4px'], ['--radius-round', 'pill']].forEach(([t, note]) => {
        const cell = el('div');
        Object.assign(cell.style, { display: 'flex', flexDirection: 'column', gap: '8px', alignItems: 'center' });
        const box = el('div');
        Object.assign(box.style, { width: '90px', height: '60px', background: 'var(--color-surface-2)', border: '1px solid var(--color-border)', borderRadius: `var(${t})` });
        cell.append(box, el('div', 'u-mono', `${t} · ${note}`));
        wrap.appendChild(cell);
    });
    return wrap;
};

export const Shadows = () => {
    const wrap = el('div');
    Object.assign(wrap.style, { display: 'flex', gap: '32px', padding: '20px' });
    ['--shadow-1', '--shadow-2'].forEach((t) => {
        const box = el('div', null, t);
        Object.assign(box.style, { display: 'grid', placeItems: 'center', width: '160px', height: '90px', background: 'var(--color-surface)', borderRadius: 'var(--radius)', boxShadow: `var(${t})`, fontFamily: 'var(--font-mono)', fontSize: '12px' });
        wrap.appendChild(box);
    });
    return wrap;
};
