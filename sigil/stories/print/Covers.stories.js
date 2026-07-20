import '../../../catalyst/assets/styles/print.css';
import { fixtureFrame } from '../_helpers.js';

/*
 * PDF covers, rendered from the real Twig templates via `app:design:dump`
 * (fixtures under sigil/fixtures/print). If a story is empty, run:
 *   make -C catalyst dump-design   (after building assets)
 */
export default {
    title: 'Print/Covers',
    parameters: { layout: 'centered' },
};

export const Front = () => fixtureFrame('/print/cover-front.html', { zoom: 0.55 });
export const Back = () => fixtureFrame('/print/cover-back.html', { zoom: 0.55 });
export const BackEpure = () => fixtureFrame('/print/cover-back-art.html', { zoom: 0.55 });
