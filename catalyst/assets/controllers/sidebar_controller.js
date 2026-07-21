import { Controller } from '@hotwired/stimulus';

/*
 * Collapse/expand the whole section sidebar. State lives on <html data-sidebar>
 * so it survives Swup navigations (transitions_controller swaps #rail/#sidenav/
 * #stage, never <html>) and is read back from localStorage on a fresh load,
 * mirroring theme_controller. The stage claims the freed width; the icon rail
 * stays, carrying the toggle. Desktop affordance: the rail (and this control)
 * is hidden below 1024px, where the sidenav shows as a block instead.
 *
 * The toggle button lives in the swapped rail, so its aria-expanded is re-synced
 * from the persisted state every time the rail (re)connects, via the Stimulus
 * targetConnected lifecycle. Progressive enhancement: with JS off the sidebar
 * stays expanded and fully usable.
 */
export default class extends Controller {
    static targets = ['toggle'];

    connect() {
        this.setState(window.localStorage.getItem('sidebar') === 'collapsed');
    }

    toggle() {
        this.setState(document.documentElement.dataset.sidebar !== 'collapsed');
    }

    // Fires on first render and after each Swup rail swap; keeps the (possibly
    // fresh) button's aria-expanded in step with the persisted state.
    toggleTargetConnected(el) {
        el.setAttribute('aria-expanded', String(document.documentElement.dataset.sidebar !== 'collapsed'));
    }

    setState(collapsed) {
        const state = collapsed ? 'collapsed' : 'expanded';
        document.documentElement.dataset.sidebar = state;
        window.localStorage.setItem('sidebar', state);
        if (this.hasToggleTarget) {
            this.toggleTarget.setAttribute('aria-expanded', String(!collapsed));
        }
    }
}
