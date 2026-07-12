/*
 * Print document bundle: the print-ready A4 stylesheet plus the small
 * progressive-enhancement controller that opens the browser print dialog.
 * Kept separate from the editor `app` bundle so the print output carries
 * no editor chrome.
 */
import { Application } from '@hotwired/stimulus';
import PrintController from './controllers/print_controller.js';
import './styles/print.css';

const application = Application.start();
application.register('print', PrintController);
