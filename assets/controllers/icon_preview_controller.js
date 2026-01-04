import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['preview', 'input'];
    static values = {
        placeholder: { type: String, default: 'https://placehold.co/90?text=?' },
    };

    connect() {
        if (this.previewTarget && !this.previewTarget.innerHTML.trim()) {
            this.setPlaceholder();
        }
    }

    triggerUpload() {
        this.inputTarget.click();
    }

    updatePreview(event) {
        const [file] = event.target.files;
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                this.previewTarget.innerHTML = `<img src="${reader.result}" alt="" class="w-full h-full object-cover rounded-lg">`;
            };
            reader.readAsDataURL(file);

            return;
        }

        this.setPlaceholder();
    }

    setPlaceholder() {
        this.previewTarget.innerHTML = `<img src="${this.placeholderValue}" alt="" class="w-full h-full object-cover rounded-lg">`;
    }
}
