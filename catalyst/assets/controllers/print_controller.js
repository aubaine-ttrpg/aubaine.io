import { Controller } from '@hotwired/stimulus';

/*
 * Opens the browser print dialog. Progressive enhancement only: the
 * "Imprimer" button is a normal element and the page is fully readable
 * with JS off; this controller just saves a menu click.
 */
export default class extends Controller {
    print() {
        window.print();
    }
}
