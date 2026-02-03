import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'grid',
        'links',
        'payload',
        'editor',
        'modeSelect',
        'skillSearchInput',
        'skillSearchResults',
        'skillSearchHidden',
        'anonFields',
        'existingFields',
        'anonCode',
        'anonName',
        'anonIcon',
        'costInput',
        'starterInput',
        'status',
    ];

    static values = {
        skills: Array,
        placeholder: String,
        initial: Object,
        readonly: Boolean,
        searchUrl: String,
    };

    connect() {
        this.nodes = new Map();
        this.linksSet = new Set();
        this.linkFromKey = null;
        this.activeCell = null;
        this.skillMap = new Map(
            (this.skillsValue || []).map((skill) => [skill.id, skill])
        );
        this.searchDebounce = null;
        this.selectedSkillMeta = null;
        this.selectedSkillLabel = '';

        this.cellIndex = new Map();
        this.gridTarget.querySelectorAll('[data-row][data-col]').forEach((cell) => {
            const key = this.getCellKey(cell);
            this.cellIndex.set(key, cell);
        });

        this.applyInitialPayload();
        this.updatePayload();
        this.drawLinks();

        this.resizeHandler = () => this.drawLinks();
        window.addEventListener('resize', this.resizeHandler);

        if (this.hasEditorTarget) {
            this.backdropHandler = (event) => {
                if (event.target === this.editorTarget) {
                    this.closeEditor();
                }
            };
            this.keydownHandler = (event) => {
                if (event.key === 'Escape' && this.editorTarget.classList.contains('is-open')) {
                    this.closeEditor();
                }
            };
            this.editorTarget.addEventListener('click', this.backdropHandler);
            document.addEventListener('keydown', this.keydownHandler);
        }
    }

    disconnect() {
        window.removeEventListener('resize', this.resizeHandler);
        if (this.backdropHandler && this.hasEditorTarget) {
            this.editorTarget.removeEventListener('click', this.backdropHandler);
        }
        if (this.keydownHandler) {
            document.removeEventListener('keydown', this.keydownHandler);
        }
    }

    handleCellClick(event) {
        if (this.readonlyValue) {
            return;
        }
        const cell = event.currentTarget;
        if (event.shiftKey) {
            this.toggleLink(cell);
            return;
        }

        this.openEditor(cell);
    }

    openEditor(cell) {
        if (!this.hasEditorTarget) {
            return;
        }
        this.activeCell = cell;
        const key = this.getCellKey(cell);
        const node = this.nodes.get(key);

        const mode = node && node.anon && !node.skillId ? 'anon' : 'existing';
        this.modeSelectTarget.value = mode;
        const skillMeta = node && node.skillMeta ? node.skillMeta : null;
        this.selectedSkillMeta = skillMeta;
        this.selectedSkillLabel = skillMeta ? this.formatSkillLabel(skillMeta) : '';
        this.skillSearchHiddenTarget.value = node && node.skillId ? node.skillId : '';
        this.skillSearchInputTarget.value = this.selectedSkillLabel;
        this.clearSearchResults();
        this.anonCodeTarget.value = node && node.anon ? node.anon.code || '' : '';
        this.anonNameTarget.value = node && node.anon ? node.anon.name || '' : '';
        this.anonIconTarget.value = node && node.anon ? node.anon.icon || '' : '';
        this.costInputTarget.value = node ? node.cost : 0;
        this.starterInputTarget.checked = node ? node.isStarter : false;

        this.updateMode();
        this.editorTarget.hidden = false;
        this.editorTarget.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => {
            this.editorTarget.classList.add('is-open');
        });
    }

    closeEditor() {
        if (!this.hasEditorTarget) {
            return;
        }
        this.editorTarget.classList.remove('is-open');
        this.editorTarget.setAttribute('aria-hidden', 'true');
        window.setTimeout(() => {
            this.editorTarget.hidden = true;
        }, 160);
        this.clearSearchResults();
        this.activeCell = null;
    }

    updateMode() {
        const mode = this.modeSelectTarget.value;
        const showExisting = mode === 'existing';
        this.existingFieldsTarget.classList.toggle('is-hidden', !showExisting);
        this.anonFieldsTarget.classList.toggle('is-hidden', showExisting);
        if (!showExisting) {
            this.skillSearchHiddenTarget.value = '';
            this.skillSearchInputTarget.value = '';
            this.selectedSkillMeta = null;
            this.selectedSkillLabel = '';
            this.clearSearchResults();
        }
    }

    saveNode() {
        if (!this.activeCell) {
            return;
        }

        const key = this.getCellKey(this.activeCell);
        const mode = this.modeSelectTarget.value;
        const cost = parseInt(this.costInputTarget.value || '0', 10);
        const isStarter = this.starterInputTarget.checked;
        const selectedSkillId = this.skillSearchHiddenTarget.value;
        const selectedMeta =
            this.selectedSkillMeta ||
            (selectedSkillId ? this.skillMap.get(selectedSkillId) : null);

        let node = null;
        if (mode === 'existing' && selectedSkillId) {
            node = {
                row: parseInt(this.activeCell.dataset.row, 10),
                col: parseInt(this.activeCell.dataset.col, 10),
                cost,
                isStarter,
                skillId: selectedSkillId,
                anon: null,
                skillMeta: selectedMeta || null,
            };
        } else if (mode === 'anon') {
            const anon = {
                code: this.anonCodeTarget.value.trim(),
                name: this.anonNameTarget.value.trim(),
                icon: this.anonIconTarget.value.trim(),
            };
            if (anon.code || anon.name || anon.icon) {
                node = {
                    row: parseInt(this.activeCell.dataset.row, 10),
                    col: parseInt(this.activeCell.dataset.col, 10),
                    cost,
                    isStarter,
                    skillId: null,
                    anon,
                };
            }
        }

        if (node) {
            this.nodes.set(key, node);
            this.updateCellDisplay(this.activeCell, node);
        } else {
            this.nodes.delete(key);
            this.clearLinksFor(key);
            this.updateCellDisplay(this.activeCell, null);
        }

        this.updatePayload();
        this.drawLinks();
        this.closeEditor();
    }

    clearNode() {
        if (!this.activeCell) {
            return;
        }

        const key = this.getCellKey(this.activeCell);
        this.nodes.delete(key);
        this.clearLinksFor(key);
        this.updateCellDisplay(this.activeCell, null);
        this.updatePayload();
        this.drawLinks();
        this.closeEditor();
    }

    toggleLink(cell) {
        if (this.readonlyValue) {
            return;
        }
        const key = this.getCellKey(cell);
        if (!this.nodes.has(key)) {
            this.setStatus('Links need two filled nodes.');
            return;
        }

        if (!this.linkFromKey) {
            this.linkFromKey = key;
            cell.classList.add('is-link-source');
            this.setStatus('Select another node to create a link.');
            return;
        }

        if (this.linkFromKey === key) {
            this.clearLinkSource();
            this.setStatus('Link canceled.');
            return;
        }

        const linkKey = this.getLinkKey(this.linkFromKey, key);
        if (this.linksSet.has(linkKey)) {
            this.linksSet.delete(linkKey);
            this.setStatus('Link removed.');
        } else {
            this.linksSet.add(linkKey);
            this.setStatus('Link added.');
        }

        this.clearLinkSource();
        this.updatePayload();
        this.drawLinks();
    }

    clearLinkSource() {
        if (this.linkFromKey) {
            const sourceCell = this.cellIndex.get(this.linkFromKey);
            if (sourceCell) {
                sourceCell.classList.remove('is-link-source');
            }
        }

        this.linkFromKey = null;
    }

    clearLinksFor(key) {
        for (const linkKey of Array.from(this.linksSet)) {
            const [fromKey, toKey] = linkKey.split('|');
            if (fromKey === key || toKey === key) {
                this.linksSet.delete(linkKey);
            }
        }
        if (this.linkFromKey === key) {
            this.clearLinkSource();
        }
    }

    updateCellDisplay(cell, node) {
        const plate = cell.querySelector('.skill-plate');
        const img = plate ? plate.querySelector('.skill-plate__image') : null;
        const code = plate ? plate.querySelector('.skill-plate__code') : null;
        const costText = plate ? plate.querySelector('.skill-plate__cost-text') : null;

        if (!node) {
            cell.classList.remove('is-filled', 'is-starter');
            cell.setAttribute('aria-pressed', 'false');
            if (img) {
                img.src = this.placeholderValue;
            }
            if (code) {
                code.textContent = 'NAME';
            }
            if (costText) {
                costText.textContent = '0';
            }
            return;
        }

        let displayCode = 'SKILL';
        let displayImage = this.placeholderValue;

        if (node.skillId) {
            const skill = node.skillMeta || this.skillMap.get(node.skillId);
            if (skill) {
                displayCode = skill.code || displayCode;
                displayImage = skill.icon || displayImage;
            }
        } else if (node.anon) {
            displayCode = node.anon.code || node.anon.name || 'ANON';
            displayImage = node.anon.icon || displayImage;
        }

        cell.classList.add('is-filled');
        cell.classList.toggle('is-starter', node.isStarter);
        cell.setAttribute('aria-pressed', 'true');
        if (img) {
            img.src = displayImage;
        }
        if (code) {
            code.textContent = displayCode;
        }
        if (costText) {
            costText.textContent = String(node.cost ?? 0);
        }
    }

    updatePayload() {
        if (!this.hasPayloadTarget) {
            return;
        }
        const nodes = Array.from(this.nodes.values()).map((node) => ({
            row: node.row,
            col: node.col,
            cost: node.cost,
            isStarter: node.isStarter,
            skillId: node.skillId || null,
            anon: node.anon || null,
        }));

        const links = Array.from(this.linksSet).map((linkKey) => {
            const [fromKey, toKey] = linkKey.split('|');
            const [fromRow, fromCol] = fromKey.split('-').map((v) => parseInt(v, 10));
            const [toRow, toCol] = toKey.split('-').map((v) => parseInt(v, 10));
            return {
                from: { row: fromRow, col: fromCol },
                to: { row: toRow, col: toCol },
            };
        });

        this.payloadTarget.value = JSON.stringify({ nodes, links });
    }

    drawLinks() {
        if (!this.hasLinksTarget) {
            return;
        }

        const svg = this.linksTarget;
        while (svg.firstChild) {
            svg.removeChild(svg.firstChild);
        }

        const gridRect = this.gridTarget.getBoundingClientRect();
        const width = gridRect.width;
        const height = gridRect.height;
        svg.setAttribute('width', width.toString());
        svg.setAttribute('height', height.toString());
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`);

        this.linksSet.forEach((linkKey) => {
            const [fromKey, toKey] = linkKey.split('|');
            const fromCell = this.cellIndex.get(fromKey);
            const toCell = this.cellIndex.get(toKey);
            if (!fromCell || !toCell) {
                return;
            }

            const from = this.getCellCenter(fromCell, gridRect);
            const to = this.getCellCenter(toCell, gridRect);

            const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', from.x.toString());
            line.setAttribute('y1', from.y.toString());
            line.setAttribute('x2', to.x.toString());
            line.setAttribute('y2', to.y.toString());
            line.setAttribute('class', 'skill-tree-builder__link');
            svg.appendChild(line);
        });
    }

    applyInitialPayload() {
        if (!this.hasInitialValue) {
            return;
        }

        const nodes = Array.isArray(this.initialValue.nodes) ? this.initialValue.nodes : [];
        nodes.forEach((node) => {
            const key = `${node.row}-${node.col}`;
            this.nodes.set(key, {
                row: node.row,
                col: node.col,
                cost: node.cost ?? 0,
                isStarter: !!node.isStarter,
                skillId: node.skillId || null,
                anon: node.anon || null,
                skillMeta: node.skill || null,
            });

            const cell = this.cellIndex.get(key);
            if (cell) {
                this.updateCellDisplay(cell, this.nodes.get(key));
            }
        });

        const links = Array.isArray(this.initialValue.links) ? this.initialValue.links : [];
        links.forEach((link) => {
            if (!link.from || !link.to) {
                return;
            }
            const fromKey = `${link.from.row}-${link.from.col}`;
            const toKey = `${link.to.row}-${link.to.col}`;
            if (fromKey === toKey) {
                return;
            }
            const linkKey = this.getLinkKey(fromKey, toKey);
            this.linksSet.add(linkKey);
        });
    }

    getCellKey(cell) {
        return `${cell.dataset.row}-${cell.dataset.col}`;
    }

    getLinkKey(a, b) {
        return a < b ? `${a}|${b}` : `${b}|${a}`;
    }

    getCellCenter(cell, gridRect) {
        const rect = cell.getBoundingClientRect();
        return {
            x: rect.left - gridRect.left + rect.width / 2,
            y: rect.top - gridRect.top + rect.height / 2,
        };
    }

    searchSkills() {
        if (this.readonlyValue || !this.hasSearchUrlValue) {
            return;
        }

        const term = this.skillSearchInputTarget.value.trim();
        if (term !== this.selectedSkillLabel) {
            this.skillSearchHiddenTarget.value = '';
            this.selectedSkillMeta = null;
        }

        if (term.length < 1) {
            this.clearSearchResults();
            return;
        }

        window.clearTimeout(this.searchDebounce);
        this.searchDebounce = window.setTimeout(() => {
            const url = `${this.searchUrlValue}?q=${encodeURIComponent(term)}`;
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then((response) => (response.ok ? response.json() : []))
                .then((results) => {
                    this.renderSearchResults(Array.isArray(results) ? results : []);
                })
                .catch(() => {
                    this.renderSearchResults([]);
                });
        }, 220);
    }

    renderSearchResults(results) {
        if (!this.hasSkillSearchResultsTarget) {
            return;
        }

        const container = this.skillSearchResultsTarget;
        container.innerHTML = '';

        if (!results.length) {
            const empty = document.createElement('div');
            empty.className = 'is-empty';
            empty.textContent = 'No skills found.';
            container.appendChild(empty);
            return;
        }

        results.forEach((skill) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = this.formatSkillLabel(skill);
            button.dataset.skillId = skill.id;
            button.dataset.skillCode = skill.code || '';
            button.dataset.skillName = skill.name || '';
            button.dataset.skillIcon = skill.icon || '';
            button.addEventListener('click', () => {
                this.selectSkillFromResult(button.dataset);
            });
            container.appendChild(button);
        });
    }

    selectSkillFromResult(dataset) {
        const skillMeta = {
            id: dataset.skillId,
            code: dataset.skillCode,
            name: dataset.skillName,
            icon: dataset.skillIcon,
        };
        this.skillSearchHiddenTarget.value = dataset.skillId || '';
        this.selectedSkillMeta = skillMeta;
        this.selectedSkillLabel = this.formatSkillLabel(skillMeta);
        this.skillSearchInputTarget.value = this.selectedSkillLabel;
        this.clearSearchResults();
    }

    clearSearchResults() {
        if (this.hasSkillSearchResultsTarget) {
            this.skillSearchResultsTarget.innerHTML = '';
        }
    }

    formatSkillLabel(skill) {
        const code = skill.code || '';
        const name = skill.name || '';
        return [code, name].filter(Boolean).join(' · ') || 'Skill';
    }

    setStatus(message) {
        if (this.hasStatusTarget) {
            this.statusTarget.textContent = message;
        }
    }
}
