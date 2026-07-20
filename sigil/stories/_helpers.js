/* Small DOM helpers shared by the stories (html-vite renderer: a story returns
   a DOM node or an HTML string). */

export function el(tag, className, html) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (html != null) node.innerHTML = html;
    return node;
}

/** Read a CSS custom property off :root so swatches reflect the tokens, not copies. */
export function readVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

/** A labelled colour swatch. `value` is any CSS colour (or a var() reference). */
export function swatch(value, label, sub) {
    const cell = el('div');
    Object.assign(cell.style, { display: 'flex', flexDirection: 'column', gap: '6px' });
    const chip = el('div');
    Object.assign(chip.style, {
        height: '64px',
        borderRadius: 'var(--radius)',
        background: value,
        border: '1px solid var(--color-border)',
    });
    cell.appendChild(chip);
    const name = el('div', 'u-mono', label);
    Object.assign(name.style, { fontSize: '12px', color: 'var(--color-text)' });
    cell.appendChild(name);
    if (sub) {
        const s = el('div', 'u-mono', sub);
        Object.assign(s.style, { fontSize: '11px', color: 'var(--color-text-muted)' });
        cell.appendChild(s);
    }
    return cell;
}

/** Responsive swatch/card grid. */
export function grid(children, min = '160px') {
    const g = el('div');
    Object.assign(g.style, {
        display: 'grid',
        gap: '16px',
        gridTemplateColumns: `repeat(auto-fill, minmax(${min}, 1fr))`,
    });
    children.forEach((c) => g.appendChild(c));
    return g;
}

export function section(title) {
    const h = el('h3', 'section-head', title);
    return h;
}

/**
 * Render a print fixture (real Twig output dumped by `app:design:dump`) scaled to
 * fit. Uses CSS zoom so the A4 leaves reflow inside the story canvas.
 */
export function fixtureFrame(path, { zoom = 0.5 } = {}) {
    const wrap = el('div');
    wrap.style.zoom = String(zoom);
    fetch(path)
        .then((r) => {
            if (!r.ok) throw new Error(`${path}: ${r.status} (run: make -C catalyst dump-design)`);
            return r.text();
        })
        .then((html) => { wrap.innerHTML = html; })
        .catch((e) => { wrap.style.zoom = '1'; wrap.appendChild(missing(e.message)); });
    return wrap;
}

/**
 * Pull a set of elements out of a print fixture and drop them onto a paper card,
 * so a single node / ability / nameplate can be previewed in isolation without
 * hand-copying its markup.
 */
export function fixtureExtract(path, selector, { limit = 0, paper = '#fffdf9', zoom = 1, gap = '28px', onItem = null } = {}) {
    const stage = el('div');
    Object.assign(stage.style, {
        display: 'flex',
        flexWrap: 'wrap',
        gap,
        alignItems: 'flex-start',
        padding: '28px',
        background: paper,
        borderRadius: 'var(--radius)',
        border: '1px solid var(--color-border)',
    });
    if (zoom !== 1) stage.style.zoom = String(zoom);
    fetch(path)
        .then((r) => {
            if (!r.ok) throw new Error(`${path}: ${r.status} (run: make -C catalyst dump-design)`);
            return r.text();
        })
        .then((html) => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            let nodes = [...doc.querySelectorAll(selector)];
            if (limit > 0) nodes = nodes.slice(0, limit);
            if (nodes.length === 0) stage.appendChild(missing(`no "${selector}" in ${path}`));
            nodes.forEach((n) => {
                if (onItem) onItem(n);
                stage.appendChild(n);
            });
        })
        .catch((e) => stage.appendChild(missing(e.message)));
    return stage;
}

export function fetchJson(path) {
    return fetch(path).then((r) => {
        if (!r.ok) throw new Error(`${path}: ${r.status} (run: make -C catalyst dump-design)`);
        return r.json();
    });
}

function missing(message) {
    const box = el('div', null, `⚠ ${message}`);
    Object.assign(box.style, {
        padding: '16px',
        fontFamily: 'var(--font-mono)',
        fontSize: '13px',
        color: 'var(--color-danger)',
        border: '1px dashed var(--color-danger)',
        borderRadius: 'var(--radius)',
    });
    return box;
}
