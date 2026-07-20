import { Controller } from '@hotwired/stimulus';
import Swup from 'swup';
import SwupHeadPlugin from '@swup/head-plugin';

/*
 * Page transitions with Swup. Swaps the shell's rail, sidebar, and stage so
 * navigation feels continuous (the stage cross-fades via @aubaine/sigil
 * page-transition.css) without replaying the intro; the top bar, theme, and
 * status persist. Because the rail and sidebar are swapped too, their active
 * states stay correct from the server render — no client-side sync needed.
 * Stimulus and Live Components reconnect automatically on content replace.
 *
 * Links opt out with data-no-swup (PDF downloads, the print view); external,
 * download, and target=_blank links are ignored by Swup by default.
 */
export default class extends Controller {
    connect() {
        if (window.__swup) {
            return;
        }
        this.swup = new Swup({
            containers: ['#rail', '#sidenav', '#stage'],
            animationSelector: '[class*="transition-"]',
            ignoreVisit: (url, { el } = {}) => Boolean(el?.closest('[data-no-swup]')),
            plugins: [new SwupHeadPlugin()],
        });
        window.__swup = this.swup;
    }

    disconnect() {
        if (this.swup) {
            this.swup.destroy();
            this.swup = null;
            window.__swup = undefined;
        }
    }
}
