import { Controller } from '@hotwired/stimulus';
import confetti from 'canvas-confetti';

/*
 * Shiny mark: a 1-in-4096 nod to shiny odds. On a full page load, the sparkly
 * gold mark (aubaine-logo-shiny.svg) rarely takes over the header logo and
 * favicon in place of the usual gold/void mark, and fireworks-style confetti
 * fires once the loading intro reveals the page. Progressive enhancement: without JS
 * you always get the normal mark. The roll happens once per hard load; a win
 * then rides across in-page (Swup) navigations because the topbar and <html>
 * are never swapped. A hard refresh re-rolls.
 *
 * The force value (from the SHINY_FORCE env var, via a Twig global) skips the
 * roll and always activates, for demos and testing.
 */
const SHINY_ODDS = 1 / 4096;

// Gold ramp + void purple, so the confetti reads as Aubaine.
const CONFETTI_COLORS = ['#efbe04', '#e1c47b', '#9d6fd1', '#c9a8f0'];

export default class extends Controller {
    static values = { url: String, force: Boolean };

    connect() {
        // Idempotent: never re-roll if the mark is already shiny this session.
        if (document.documentElement.classList.contains('is-shiny')) {
            return;
        }
        if (!this.forceValue && Math.random() >= SHINY_ODDS) {
            return;
        }

        document.documentElement.classList.add('is-shiny');

        // Swap the header mark: hide the inline morph SVG, show the shiny mark behind it.
        const mark = document.querySelector('.aubaine-switch');
        if (mark && this.hasUrlValue) {
            const svg = mark.querySelector('.aubaine-switch__svg');
            if (svg) {
                svg.style.display = 'none';
            }
            mark.style.background = `center / contain no-repeat url("${this.urlValue}")`;
        }

        // Swap the favicon.
        const icon = document.querySelector('link[rel~="icon"]');
        if (icon && this.hasUrlValue) {
            icon.setAttribute('href', this.urlValue);
        }

        this.celebrate();
    }

    // Fire once the page is visible. On a hard load the blackhole intro covers
    // the page (html.intro-active); wait for it to reveal so the burst is not
    // wasted behind the overlay.
    celebrate() {
        const root = document.documentElement;
        if (!root.classList.contains('intro-active')) {
            this.fireConfetti();
            return;
        }
        this.introObserver = new MutationObserver(() => {
            if (!root.classList.contains('intro-active')) {
                this.stopObserving();
                this.fireConfetti();
            }
        });
        this.introObserver.observe(root, { attributes: true, attributeFilter: ['class'] });
    }

    // A short fireworks volley: repeated 360-degree bursts from both sides,
    // tapering off. Skipped entirely under prefers-reduced-motion.
    fireConfetti() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const duration = 3000;
        const animationEnd = Date.now() + duration;
        const defaults = { startVelocity: 32, spread: 360, ticks: 70, colors: CONFETTI_COLORS };
        const randomInRange = (min, max) => Math.random() * (max - min) + min;

        this.fireworks = window.setInterval(() => {
            const timeLeft = animationEnd - Date.now();
            if (timeLeft <= 0) {
                this.stopFireworks();
                return;
            }
            // Fewer particles as the volley winds down.
            const particleCount = 60 * (timeLeft / duration);
            // Launch from both sides, a touch above the top so they rain down.
            confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } });
            confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } });
        }, 250);
    }

    stopFireworks() {
        if (this.fireworks) {
            window.clearInterval(this.fireworks);
            this.fireworks = null;
        }
    }

    stopObserving() {
        if (this.introObserver) {
            this.introObserver.disconnect();
            this.introObserver = null;
        }
    }

    // Only tears down the pending intro observer; the swapped mark and favicon
    // are meant to persist for the session. The <html> host is not swapped by
    // Swup, so this runs only on full page teardown.
    disconnect() {
        this.stopObserving();
        this.stopFireworks();
    }
}
