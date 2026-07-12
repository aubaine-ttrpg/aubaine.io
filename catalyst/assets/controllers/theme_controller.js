import { Controller } from '@hotwired/stimulus';

/*
 * Light/dark toggle. Progressive enhancement: without JS the page follows the
 * OS preference (prefers-color-scheme); this persists an explicit choice.
 */
export default class extends Controller {
    connect() {
        const saved = window.localStorage.getItem('theme');
        if (saved) {
            document.documentElement.dataset.theme = saved;
        }
    }

    toggle() {
        const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.theme = next;
        window.localStorage.setItem('theme', next);
    }
}
