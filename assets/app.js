import './stimulus_bootstrap.js';
import TomSelect from 'tom-select/dist/js/tom-select.complete.js';
import 'tom-select/dist/css/tom-select.css';
import './styles/app.css';

const placeholderImg = 'https://placehold.co/90?text=?';

document.addEventListener('DOMContentLoaded', () => {
    const tomSelectInstances = new Map();

    // Tom Select for multi-selects
    document.querySelectorAll('[data-multi-select]').forEach((el) => {
        const instance = new TomSelect(el, {
            plugins: ['remove_button'],
            persist: false,
            create: false,
            maxItems: null,
            hideSelected: true,
            closeAfterSelect: false,
        });
        tomSelectInstances.set(el, instance);

        // Update tags count if applicable
        if (el.dataset.multiSelect === 'tags') {
            const counter = document.querySelector('[data-tags-count]');
            const update = () => {
                if (counter) {
                    counter.textContent = instance.items.length.toString();
                }
            };
            instance.on('change', update);
            update();
        }
    });

    // Material toggle
    const materialToggle = document.querySelector('[data-material-toggle] input[type="checkbox"]');
    const materialString = document.querySelector('[data-material-string]');
    const updateMaterial = () => {
        if (!materialString) return;
        materialString.classList.toggle('hidden', !(materialToggle && materialToggle.checked));
    };
    if (materialToggle) {
        materialToggle.addEventListener('change', updateMaterial);
        updateMaterial();
    }

    // Icon preview
    const iconInputs = document.querySelectorAll('[data-skill-form] input[type="file"]');
    iconInputs.forEach((input) => {
        const preview = input.closest('[data-skill-form]').querySelector('[data-icon-preview]');
        const setPlaceholder = () => {
            if (preview) {
                preview.innerHTML = `<img src="${placeholderImg}" alt="" class="w-full h-full object-cover rounded-lg">`;
            }
        };
        if (preview) {
            if (!preview.querySelector('img')) {
                setPlaceholder();
            }
            preview.addEventListener('click', () => input.click());
        }
        input.addEventListener('change', (e) => {
            const [file] = e.target.files;
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = () => {
                    preview.innerHTML = `<img src="${reader.result}" alt="" class="w-full h-full object-cover rounded-lg">`;
                };
                reader.readAsDataURL(file);
            } else {
                setPlaceholder();
            }
        });
    });

    // Auto-dismiss alerts
    document.querySelectorAll('[data-alert]').forEach((alert) => {
        setTimeout(() => {
            alert.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Toggle action-only fields based on selected type
    const typeSelect = document.querySelector('[name$="[type]"]');
    const actionCard = document.querySelector('[data-action-card]');
    const actionFields = document.querySelectorAll('[data-action-only]');
    const actionTypes = typeSelect?.dataset.actionTypes
        ? JSON.parse(typeSelect.dataset.actionTypes)
        : ['action', 'bonus', 'reaction', 'attack'];

    const toggleActionFields = () => {
        const currentType = typeSelect ? typeSelect.value : '';
        const isAction = actionTypes.includes(currentType);

        if (actionCard) {
            actionCard.classList.toggle('hidden', !isAction);
        }

        actionFields.forEach((field) => {
            const instance = tomSelectInstances.get(field);
            if (!isAction) {
                if (instance) {
                    instance.clear();
                    instance.disable();
                }
                if (field.type === 'checkbox') {
                    field.checked = false;
                } else {
                    field.value = '';
                }
                field.setAttribute('disabled', 'disabled');
            } else {
                field.removeAttribute('disabled');
                if (instance) {
                    instance.enable();
                }
            }
        });
    };

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleActionFields);
        toggleActionFields();
    }
});
