import { Controller } from '@hotwired/stimulus';

/*
 * Light/dark toggle, triggered by the header logo. Progressive enhancement:
 * without JS the page follows the OS preference (prefers-color-scheme); this
 * persists an explicit choice. The switch is scoped to the header: the logo
 * morphs light<->void in place while the page colours ease across, rather than a
 * full-screen takeover (that stays for the loading intro).
 */
const MORPH_MS = 600;

export default class extends Controller {
    connect() {
        const saved = window.localStorage.getItem('theme');
        if (saved) {
            document.documentElement.dataset.theme = saved;
        }
    }

    toggle(event) {
        if (this.switching) {
            return;
        }
        const current = document.documentElement.dataset.theme;
        const isDark = current
            ? current === 'dark'
            : window.matchMedia('(prefers-color-scheme: dark)').matches;
        const next = isDark ? 'light' : 'dark';

        this.switching = true;
        const root = document.documentElement;
        const badge = event?.currentTarget?.querySelector('.aubaine-switch');

        root.classList.add('theme-changing');
        if (badge) badge.classList.add('is-morphing');

        root.dataset.theme = next;
        window.localStorage.setItem('theme', next);

        this.reset = window.setTimeout(() => {
            root.classList.remove('theme-changing');
            if (badge) badge.classList.remove('is-morphing');
            this.switching = false;
        }, MORPH_MS);
    }

    disconnect() {
        window.clearTimeout(this.reset);
        document.documentElement.classList.remove('theme-changing');
        this.switching = false;
    }
}
