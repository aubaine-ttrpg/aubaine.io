import { Controller } from '@hotwired/stimulus';

/*
 * Drives the live editor's center preview: an <iframe> onto the print route
 * (the exact HTML Gotenberg turns into the PDF). The Live Component bumps the
 * `nonce` value whenever the book changes; we reload the iframe (debounced) so
 * the preview follows edits without reloading the whole page. Selecting a page
 * scrolls to it; the toolbar scales/paginates and opens a full-screen modal.
 *
 * The iframe subtree carries `data-live-ignore`, so Live never morphs it and
 * only this controller reloads it.
 */
const A4_WIDTH_PX = 794; // 210mm at 96dpi
const RELOAD_DEBOUNCE_MS = 300;

export default class extends Controller {
    static targets = ['frame', 'wrapper', 'stage', 'position', 'dialog', 'modalFrame'];
    static values = { nonce: Number, selected: String, src: String };

    connect() {
        this.reloadTimer = null;
        this.bodyObserver = null;
        this.scale = 1;
        this.pages = [];
        this.nonceReady = false;

        this.onFrameLoad = this.handleFrameLoad.bind(this);
        this.frameTarget.addEventListener('load', this.onFrameLoad);

        this.stageObserver = new ResizeObserver(() => this.fit());
        this.stageObserver.observe(this.stageTarget);
    }

    disconnect() {
        if (this.reloadTimer) {
            clearTimeout(this.reloadTimer);
        }
        this.frameTarget.removeEventListener('load', this.onFrameLoad);
        this.stageObserver.disconnect();
        if (this.bodyObserver) {
            this.bodyObserver.disconnect();
        }
        if (this.hasDialogTarget && this.dialogTarget.open) {
            this.dialogTarget.close();
        }
    }

    // The first callback is the initial value; the server already rendered the
    // iframe with the right content, so only reload on later changes.
    nonceValueChanged() {
        if (!this.nonceReady) {
            this.nonceReady = true;

            return;
        }
        this.scheduleReload();
    }

    selectedValueChanged() {
        this.scrollToSelected();
    }

    refresh() {
        this.reload();
    }

    scheduleReload() {
        if (this.reloadTimer) {
            clearTimeout(this.reloadTimer);
        }
        this.reloadTimer = setTimeout(() => this.reload(), RELOAD_DEBOUNCE_MS);
    }

    reload() {
        const separator = this.srcValue.includes('?') ? '&' : '?';
        this.frameTarget.src = `${this.srcValue}${separator}v=${this.nonceValue}`;
    }

    handleFrameLoad() {
        const doc = this.frameDocument();
        if (!doc) {
            return;
        }

        this.pages = Array.from(doc.querySelectorAll('.page'));

        // Re-fit whenever the print doc reflows (web fonts load, abilities paginate).
        if (this.bodyObserver) {
            this.bodyObserver.disconnect();
        }
        this.bodyObserver = new ResizeObserver(() => this.fit());
        this.bodyObserver.observe(doc.body);

        this.fit();
        this.scrollToSelected();
        this.updatePosition();
    }

    fit() {
        const doc = this.frameDocument();
        if (!doc) {
            return;
        }

        const contentHeight = doc.body.scrollHeight;
        this.frameTarget.style.width = `${A4_WIDTH_PX}px`;
        this.frameTarget.style.height = `${contentHeight}px`;

        this.scale = Math.min(1, this.stageTarget.clientWidth / A4_WIDTH_PX);
        this.frameTarget.style.transformOrigin = 'top left';
        this.frameTarget.style.transform = `scale(${this.scale})`;

        this.wrapperTarget.style.width = `${A4_WIDTH_PX * this.scale}px`;
        this.wrapperTarget.style.height = `${contentHeight * this.scale}px`;
    }

    frameDocument() {
        try {
            return this.frameTarget.contentDocument;
        } catch {
            return null;
        }
    }

    scrollToSelected() {
        const doc = this.frameDocument();
        if (!doc || !this.selectedValue) {
            return;
        }
        const leaf = doc.querySelector(`[data-page-id="${CSS.escape(this.selectedValue)}"]`);
        if (!leaf) {
            return;
        }
        this.stageTarget.scrollTo({ top: leaf.offsetTop * this.scale, behavior: 'smooth' });
        this.updatePosition();
    }

    prev() {
        this.step(-1);
    }

    next() {
        this.step(1);
    }

    step(delta) {
        if (!this.pages.length) {
            return;
        }
        const index = Math.max(0, Math.min(this.pages.length - 1, this.currentIndex() + delta));
        this.stageTarget.scrollTo({ top: this.pages[index].offsetTop * this.scale, behavior: 'smooth' });
        this.setPosition(index);
    }

    currentIndex() {
        const top = this.stageTarget.scrollTop / (this.scale || 1);
        let index = 0;
        this.pages.forEach((page, i) => {
            if (page.offsetTop <= top + 4) {
                index = i;
            }
        });

        return index;
    }

    updatePosition() {
        this.setPosition(this.currentIndex());
    }

    setPosition(index) {
        if (!this.hasPositionTarget) {
            return;
        }
        this.positionTarget.textContent = this.pages.length ? `${index + 1} / ${this.pages.length}` : '–';
    }

    openModal(event) {
        event.preventDefault();
        if (!this.hasDialogTarget) {
            return;
        }
        if (!this.modalFrameTarget.getAttribute('src')) {
            this.modalFrameTarget.src = this.srcValue;
        }
        this.dialogTarget.showModal();
    }

    closeModal() {
        if (this.hasDialogTarget && this.dialogTarget.open) {
            this.dialogTarget.close();
        }
    }

    backdropClose(event) {
        if (event.target === this.dialogTarget) {
            this.closeModal();
        }
    }

    printModal() {
        this.modalFrameTarget.contentWindow?.print();
    }

    // Guards a Live action button (e.g. delete): cancels the action if the user
    // declines. Runs before live#action in the data-action list, so
    // stopImmediatePropagation prevents the action from firing.
    confirm(event) {
        const message = event.params.message;
        if (message && !window.confirm(message)) {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
    }
}
