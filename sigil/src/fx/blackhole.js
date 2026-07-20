/*
 * The light <-> void "blackhole" morph, ported to vanilla JS (the mock's React
 * runtime is not carried over). One engine drives two uses:
 *
 *   - The intro: direction 'to-void', hold: true. The light crest holds and
 *     spins for as long as the page is actually loading (assets + webfonts),
 *     then collapses into the void and reveals the page.
 *   - The theme switch: direction 'to-void' (light->dark) or 'to-light'
 *     (dark->light, the reverse), no hold, a touch faster. onCovered flips the
 *     theme while the screen is covered, so the change is hidden.
 *
 * mountBlackhole(target, options) -> { destroy(), replay() }
 *   direction     'to-void' | 'to-light'
 *   hold          hold the first phase until the page has loaded (intro)
 *   minLoadingMs  floor for the hold so a cached load still shows a light beat
 *   speed         duration multiplier (theme switch uses < 1 to feel snappier)
 *   showEnter     show an explicit Enter button instead of auto-revealing
 *   onCovered     called once the screen is covered — flip data-theme here
 *   onComplete    called once the page is revealed
 */

const STAR_PATH = 'M50,24 L54.5,45.5 L76,50 L54.5,54.5 L50,76 L45.5,54.5 L24,50 L45.5,45.5 Z';

// Base phase durations (ms), before the `speed` multiplier. The intro is purely
// visual (no captions), so the beats are even. Only the first load + hard
// refreshes pay this; Swup navigations do not replay the intro.
const T = { loadBeat: 40, collapse: 400, void: 400, bloom: 400, enter: 240, tween: 400 };

function template(showEnter) {
    const enter = showEnter
        ? '<button type="button" class="ab-intro__enter">Enter the codex ›</button>'
        : '';
    return `
    <div class="ab-intro__stage">
      <div class="ab-intro__stage-inner">
        <div class="ab-intro__scene">
          <div class="ab-intro__halo"></div>
          <div class="ab-intro__fxburst">
            <div class="ab-intro__shock"></div>
          </div>
          <svg class="ab-intro__mark" viewBox="0 0 100 100" aria-hidden="true">
            <g class="ab-intro__hex" style="transform:rotate(0deg)">
              <polygon class="ab-intro__frame" points="50,4 90,27 90,73 50,96 10,73 10,27"></polygon>
              <polygon class="ab-intro__inner" points="50,13 82,31 82,69 50,87 18,69 18,31"></polygon>
            </g>
            <path class="ab-intro__star" d="${STAR_PATH}"></path>
            <circle class="ab-intro__flash" cx="50" cy="50" r="9"></circle>
            <g class="ab-intro__orbits">
              <ellipse cx="50" cy="50" rx="28" ry="9" fill="none" stroke="#b98fe0" stroke-width="2.2" transform="rotate(-24 50 50)"></ellipse>
              <ellipse cx="50" cy="50" rx="21" ry="6.5" fill="none" stroke="#7b4fb0" stroke-width="1.4" transform="rotate(-24 50 50)"></ellipse>
              <ellipse class="ab-intro__glint1" cx="50" cy="50" rx="28" ry="9" fill="none" stroke="#f0e6ff" stroke-width="2.2" stroke-linecap="round" transform="rotate(-24 50 50)"></ellipse>
              <ellipse class="ab-intro__glint2" cx="50" cy="50" rx="21" ry="6.5" fill="none" stroke="#e4ccff" stroke-width="1.4" stroke-linecap="round" transform="rotate(-24 50 50)"></ellipse>
            </g>
          </svg>
          <div class="ab-intro__horizon">
            <div class="ab-intro__horizon-in">
              <div class="ab-intro__bloom"></div>
              <div class="ab-intro__sheen"></div>
              <div class="ab-intro__photon"></div>
              <div class="ab-intro__core"></div>
            </div>
          </div>
        </div>
        <div class="ab-intro__actions">${enter}</div>
      </div>
    </div>`;
}

function prefersReducedMotion() {
    return typeof window !== 'undefined'
        && window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

/** Resolves once the page has loaded (assets + webfonts), with a small floor. */
function whenPageReady(minMs) {
    const floor = new Promise((resolve) => window.setTimeout(resolve, minMs));
    const loaded = document.readyState === 'complete'
        ? Promise.resolve()
        : new Promise((resolve) => window.addEventListener('load', resolve, { once: true }));
    const fonts = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();
    return Promise.all([floor, loaded, fonts]);
}

export function mountBlackhole(target, options = {}) {
    const opts = {
        direction: 'to-void',
        hold: false,
        minLoadingMs: 120,
        speed: 1,
        showEnter: false,
        onCovered: () => {},
        onComplete: () => {},
        ...options,
    };
    const scale = (ms) => ms * opts.speed;

    const root = target;
    root.classList.add('ab-intro');
    root.dataset.dir = opts.direction;
    root.style.setProperty('--ab-speed', String(opts.speed));
    root.innerHTML = template(opts.showEnter);

    const hexEl = root.querySelector('.ab-intro__hex');
    const enterEl = root.querySelector('.ab-intro__enter');
    const burstEl = root.querySelector('.ab-intro__fxburst');

    let timers = [];
    let raf = 0;
    let angle = 0;
    let spinning = false;
    let last = 0;
    let done = false;
    let destroyed = false;
    let covered = false;

    const later = (fn, ms) => {
        const id = window.setTimeout(fn, ms);
        timers.push(id);
        return id;
    };
    const clearTimers = () => {
        timers.forEach((id) => window.clearTimeout(id));
        timers = [];
    };

    const setPhase = (phase) => {
        root.dataset.phase = phase;
    };

    const markCovered = () => {
        if (covered) return;
        covered = true;
        opts.onCovered();
    };

    const applySpin = () => {
        if (hexEl) hexEl.style.transform = `rotate(${angle}deg)`;
    };
    const spinLoop = (ts) => {
        if (!spinning) return;
        if (!last) last = ts;
        angle += (ts - last) / 1000 * 130;
        last = ts;
        applySpin();
        raf = requestAnimationFrame(spinLoop);
    };
    const startSpin = () => {
        spinning = true;
        last = 0;
        raf = requestAnimationFrame(spinLoop);
    };
    const tweenHome = () => {
        spinning = false;
        cancelAnimationFrame(raf);
        let from = ((angle % 360) + 360) % 360;
        if (from > 180) from -= 360;
        const dur = scale(T.tween);
        const t0 = performance.now();
        const step = (now) => {
            const k = Math.min(1, (now - t0) / dur);
            angle = from * (1 - (1 - Math.pow(1 - k, 3)));
            applySpin();
            if (k < 1) raf = requestAnimationFrame(step);
        };
        raf = requestAnimationFrame(step);
    };

    const spawnSparks = () => {
        const colors = ['#e1c47b', '#c9a8f0', '#b98fe0'];
        for (let i = 0; i < 14; i += 1) {
            const a = (i / 14) * Math.PI * 2;
            const dist = 90 + ((i * 37) % 50);
            const size = 3 + ((i * 13) % 4);
            const spark = document.createElement('div');
            spark.className = 'ab-intro__spark';
            spark.style.width = `${size}px`;
            spark.style.height = `${size}px`;
            spark.style.margin = `${-size / 2}px 0 0 ${-size / 2}px`;
            spark.style.background = colors[i % 3];
            spark.style.setProperty('--tx', `${Math.cos(a) * dist}px`);
            spark.style.setProperty('--ty', `${Math.sin(a) * dist}px`);
            spark.style.animationDelay = `${(i % 4) * 14}ms`;
            burstEl.appendChild(spark);
        }
    };

    const finish = () => {
        if (done) return;
        done = true;
        markCovered();
        setPhase('entered');
        opts.onComplete();
    };

    const enter = () => {
        if (root.dataset.phase === 'entering' || done) return;
        clearTimers();
        setPhase('entering');
        spawnSparks();
        later(finish, scale(T.enter));
    };

    const sequenceToVoid = () => {
        if (destroyed) return;
        setPhase('collapse');
        markCovered();
        tweenHome();
        later(() => setPhase('void'), scale(T.collapse));
        if (!opts.showEnter) {
            later(enter, scale(T.collapse + T.void));
        }
    };

    const sequenceToLight = () => {
        if (destroyed) return;
        setPhase('void');
        markCovered();
        later(() => setPhase('bloom'), scale(T.void));
        if (!opts.showEnter) {
            later(enter, scale(T.void + T.bloom));
        }
    };

    const start = () => {
        clearTimers();
        cancelAnimationFrame(raf);
        done = false;
        covered = false;
        angle = 0;
        applySpin();
        while (burstEl.childElementCount > 1) burstEl.lastElementChild.remove();

        if (prefersReducedMotion()) {
            finish();
            return;
        }

        if (opts.direction === 'to-light') {
            sequenceToLight();
            return;
        }

        // to-void: a light beat, then the collapse. The intro holds that beat
        // until the page has finished loading; the theme switch keeps it brief.
        setPhase('loading');
        startSpin();
        if (opts.hold) {
            whenPageReady(opts.minLoadingMs).then(() => {
                if (!destroyed && root.dataset.phase === 'loading') sequenceToVoid();
            });
        } else {
            later(sequenceToVoid, scale(T.loadBeat));
        }
    };

    if (enterEl) {
        enterEl.addEventListener('click', enter);
    }

    start();

    return {
        replay: start,
        destroy() {
            destroyed = true;
            clearTimers();
            cancelAnimationFrame(raf);
            spinning = false;
            if (enterEl) enterEl.removeEventListener('click', enter);
            root.classList.remove('ab-intro');
            delete root.dataset.phase;
            delete root.dataset.dir;
            root.innerHTML = '';
        },
    };
}

export default mountBlackhole;
