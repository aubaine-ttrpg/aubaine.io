import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['tab', 'panel'];
    static values = {
        active: String,
    };

    connect() {
        if (!this.activeValue) {
            this.activeValue = this.tabTargets[0]?.dataset.locale || 'fr';
        }
        this.showActive();
    }

    switch(event) {
        const locale = event.currentTarget.dataset.locale;
        if (!locale) {
            return;
        }
        this.activeValue = locale;
        this.showActive();
    }

    showActive() {
        this.tabTargets.forEach((tab) => {
            const isActive = tab.dataset.locale === this.activeValue;
            tab.classList.toggle('btn-primary', isActive);
            tab.classList.toggle('btn-ghost', !isActive);
        });

        this.panelTargets.forEach((panel) => {
            const isActive = panel.dataset.locale === this.activeValue;
            panel.classList.toggle('hidden', !isActive);
        });
    }
}
