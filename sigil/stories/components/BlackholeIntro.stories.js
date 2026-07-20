import { mountBlackhole } from '../../src/fx/blackhole.js';
import { el } from '../_helpers.js';

export default {
    title: 'Effects/Blackhole',
    parameters: { layout: 'fullscreen' },
};

/**
 * The light <-> void morph. The intro (to-void, hold) plays it slow while the
 * page loads; the theme switch reuses the same animation (and its reverse,
 * to-light) faster. Boxed here with a Replay control.
 */
function demo(opts) {
    const frame = el('div');
    Object.assign(frame.style, {
        position: 'relative',
        height: '600px',
        overflow: 'hidden',
        borderRadius: 'var(--radius)',
        background: opts.direction === 'to-light' ? '#efe6d5' : '#07050b',
    });

    const page = el('div');
    Object.assign(page.style, {
        position: 'absolute', inset: '0', display: 'grid', placeItems: 'center',
        textAlign: 'center', color: 'var(--color-text)',
    });
    page.innerHTML = `
      <div>
        <span class="aubaine-logo aubaine-logo--lg" role="img" aria-label="Aubaine"></span>
        <h1 style="font-family:var(--font-display);letter-spacing:2px;margin:16px 0 8px">The Great Codex</h1>
        <p style="font-family:var(--font-garamond);font-style:italic;color:var(--color-text-muted)">The archive is open.</p>
      </div>`;
    frame.appendChild(page);

    const overlay = el('div');
    frame.appendChild(overlay);
    let controller = mountBlackhole(overlay, opts);
    overlay.style.position = 'absolute';

    const replay = el('button', 'btn btn--ghost', '↻ Replay');
    Object.assign(replay.style, { position: 'absolute', top: '12px', right: '12px', zIndex: '10000' });
    replay.addEventListener('click', () => {
        overlay.style.position = 'absolute';
        controller.replay();
    });
    frame.appendChild(replay);

    return frame;
}

// Intro: holds the light beat, then collapses to void (hold resolves fast in
// Storybook since the page is already loaded — minLoadingMs gives a visible beat).
export const Intro = () => demo({ direction: 'to-void', hold: true, minLoadingMs: 1200 });
export const ThemeToVoid = () => demo({ direction: 'to-void', speed: 0.7 });
export const ThemeToLight = () => demo({ direction: 'to-light', speed: 0.7 });
export const WaitForEnter = () => demo({ direction: 'to-void', showEnter: true, minLoadingMs: 400 });
