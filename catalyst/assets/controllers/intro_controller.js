import { Controller } from '@hotwired/stimulus';
import { mountBlackhole } from '@aubaine/sigil/fx/blackhole.js';

/*
 * The light -> void blackhole intro. Mounts once per full page load on a
 * fixed overlay, then reveals the page. This controller sits outside the Swup
 * swap container, so it stays connected across Swup navigations and the intro
 * does NOT replay between pages; a hard refresh reloads the page and replays it
 * (see docs/adr/0002-page-transitions-and-play-once-intro.md).
 */
export default class extends Controller {
    connect() {
        this.overlay = document.createElement('div');
        document.body.appendChild(this.overlay);
        document.documentElement.classList.add('intro-active');
        this.intro = mountBlackhole(this.overlay, {
            direction: 'to-void',
            hold: true,
            // Hold the light animation at least 0.4s (or the real load, whichever
            // is longer); collapse and void beats are ~0.4s each. Only the first
            // load + hard refreshes pay this, since Swup does not replay the intro.
            minLoadingMs: 400,
            onComplete: () => this.reveal(),
        });
    }

    reveal() {
        document.documentElement.classList.remove('intro-active');
        if (this.overlay) {
            this.overlay.remove();
            this.overlay = null;
        }
    }

    disconnect() {
        if (this.intro) {
            this.intro.destroy();
            this.intro = null;
        }
        this.reveal();
    }
}
