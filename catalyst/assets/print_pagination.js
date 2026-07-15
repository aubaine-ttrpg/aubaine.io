/*
 * Ability-page pagination for the print document.
 *
 * The server renders every ability of a tree into one `[data-abilities-page]`
 * leaf. Only the rendering engine knows each entry's true height (it depends on
 * description wrapping, the loaded webfonts and the characteristic badges), so we
 * measure here and pack the entries left-column, then right-column, then onto a
 * cloned page. Run this after `document.fonts.ready`: heights shift once the
 * webfonts settle.
 *
 * The two columns are the CSS `.ability-cols { column-count: 2; column-fill: auto }`
 * box; `column-fill: auto` already fills left-then-right within a page. We only
 * decide where a page ends, keeping each column's content within its height so the
 * box's `overflow: hidden` never clips.
 */

/** Paginate every ability leaf in the document. */
export function paginateAbilities() {
    document.querySelectorAll('[data-abilities-page]').forEach(paginateLeaf);
}

function paginateLeaf(sourcePage) {
    const cols = sourcePage.querySelector('.ability-cols');
    if (!cols) {
        return;
    }

    const abilities = Array.from(cols.querySelectorAll(':scope > .ability'));
    if (0 === abilities.length) {
        return;
    }

    // Column height and entry heights are both read inside the zoomed planche, so
    // the comparison is zoom-invariant. Include each entry's bottom margin (the gap
    // to the next one) so a column is never over-packed.
    const colHeight = cols.clientHeight;
    const heights = abilities.map((el) => {
        const marginBottom = parseFloat(getComputedStyle(el).marginBottom) || 0;

        return el.offsetHeight + marginBottom;
    });

    // Greedy fill: column 0, then column 1, then a new page. An entry taller than a
    // whole column is placed alone (its description flows via break-inside: auto).
    const pages = [[]];
    let column = 0;
    let used = 0;
    abilities.forEach((el, i) => {
        if (used > 0 && used + heights[i] > colHeight) {
            column += 1;
            used = 0;
            if (column === 2) {
                pages.push([]);
                column = 0;
            }
        }
        pages[pages.length - 1].push(el);
        used += heights[i];
    });

    // Page 1 reuses the source leaf; further pages are clones inserted after it, so
    // the header, footer and paper background carry over. Moving an entry with
    // appendChild detaches it from wherever it was.
    let previous = sourcePage;
    pages.forEach((entries, index) => {
        let page = sourcePage;
        let target = cols;
        if (index > 0) {
            page = sourcePage.cloneNode(true);
            target = page.querySelector('.ability-cols');
            previous.after(page);
        }
        target.replaceChildren(...entries);

        const pageNumber = page.querySelector('[data-page-number]');
        if (pageNumber) {
            pageNumber.textContent = `${index + 1} / ${pages.length}`;
        }
        previous = page;
    });
}
