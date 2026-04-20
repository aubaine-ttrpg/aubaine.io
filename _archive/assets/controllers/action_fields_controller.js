import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['type', 'card', 'actionField'];

    connect() {
        if (!this.hasTypeTarget) {
            return;
        }

        this.boundToggle = this.toggle.bind(this);
        this.typeTarget.addEventListener('change', this.boundToggle);
        this.toggle();
    }

    disconnect() {
        if (this.boundToggle && this.hasTypeTarget) {
            this.typeTarget.removeEventListener('change', this.boundToggle);
        }
    }

    get actionTypes() {
        const raw = this.typeTarget.dataset.actionTypes;
        if (!raw) {
            return ['action', 'bonus', 'reaction', 'attack'];
        }

        try {
            return JSON.parse(raw);
        } catch (e) {
            return ['action', 'bonus', 'reaction', 'attack'];
        }
    }

    toggle() {
        const currentType = this.typeTarget.value;
        const isAction = this.actionTypes.includes(currentType);

        if (this.hasCardTarget) {
            this.cardTarget.classList.toggle('hidden', !isAction);
        }

        this.actionFieldTargets.forEach((field) => {
            const tom = field.tomselect;

            if (!isAction) {
                if (tom) {
                    tom.clear();
                    tom.disable();
                }
                if (field.type === 'checkbox') {
                    field.checked = false;
                } else {
                    field.value = '';
                }
                field.setAttribute('disabled', 'disabled');
            } else {
                field.removeAttribute('disabled');
                if (tom) {
                    tom.enable();
                }
            }
        });
    }
}
