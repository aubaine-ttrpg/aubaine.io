import '../../../catalyst/assets/styles/print.css';
import { fixtureFrame } from '../_helpers.js';

/*
 * The skill-tree planche + its paginated ability page, rendered from the real
 * Twig templates by `app:design:dump`. (Column pagination is a print-time JS
 * pass; the static fixture shows the planche and the ability column layout.)
 */
export default {
    title: 'Print/Skill tree',
    parameters: { layout: 'centered' },
};

export const Planche = () => fixtureFrame('/print/skill-tree.html', { zoom: 0.55 });
