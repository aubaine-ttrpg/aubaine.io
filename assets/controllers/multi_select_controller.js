import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select/dist/js/tom-select.complete.js';

export default class extends Controller {
    static values = {
        type: { type: String, default: '' },
    };

    connect() {
        this.instance = new TomSelect(this.element, {
            plugins: ['remove_button'],
            persist: false,
            create: false,
            maxItems: null,
            hideSelected: true,
            closeAfterSelect: false,
        });

        if (this.typeValue === 'tags') {
            this.counter = document.querySelector('[data-tags-count]');
            this.instance.on('change', () => this.updateTagsCount());
            this.updateTagsCount();
        }
    }

    disconnect() {
        if (this.instance) {
            this.instance.destroy();
        }
    }

    updateTagsCount() {
        if (!this.counter || !this.instance) {
            return;
        }

        this.counter.textContent = this.instance.items.length.toString();
    }
}
