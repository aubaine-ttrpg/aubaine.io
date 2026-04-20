import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content', 'list'];
    static values = {
        headings: {
            type: String,
            default: 'h2, h3',
        },
    };

    connect() {
        this.build();
    }

    build() {
        if (!this.hasContentTarget || !this.hasListTarget) {
            return;
        }

        const selector = this.headingsValue;
        const headings = Array.from(this.contentTarget.querySelectorAll(selector))
            .filter((heading) => heading.textContent && heading.textContent.trim().length > 0);

        this.listTarget.innerHTML = '';

        const fragment = document.createDocumentFragment();
        headings.forEach((heading) => {
            const id = this.ensureId(heading);
            if (!id) {
                return;
            }

            const item = document.createElement('li');
            const link = document.createElement('a');
            link.href = `#${id}`;
            link.textContent = heading.textContent.trim();
            link.className = this.linkClass(heading.tagName);
            item.appendChild(link);
            fragment.appendChild(item);
        });

        this.listTarget.appendChild(fragment);
    }

    onListClick(event) {
        const link = event.target.closest('a[href^="#"]');
        if (!link) {
            return;
        }

        const id = link.getAttribute('href')?.slice(1);
        if (!id) {
            return;
        }

        const target = document.getElementById(id);
        if (!target) {
            return;
        }

        event.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        history.replaceState(null, '', `#${id}`);
    }

    ensureId(heading) {
        if (heading.id) {
            return heading.id;
        }

        const base = this.slugify(heading.textContent || '');
        if (!base) {
            return null;
        }

        let id = base;
        let counter = 2;
        while (document.getElementById(id)) {
            id = `${base}-${counter}`;
            counter += 1;
        }

        heading.id = id;
        return id;
    }

    slugify(text) {
        return text
            .toLowerCase()
            .replace(/['"]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    linkClass(tagName) {
        if (tagName === 'H3') {
            return 'text-sm text-slate-400 hover:text-sky-300 ml-4 block';
        }

        return 'text-sm text-slate-200 hover:text-sky-300 block';
    }
}
