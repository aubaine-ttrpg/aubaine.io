import '../../../catalyst/assets/styles/print.css';
import { fixtureExtract } from '../_helpers.js';

/*
 * Ability entries and characteristic badges pulled out of the skill-tree fixture,
 * so a single entry can be previewed without re-copying the macro markup.
 */
export default {
    title: 'Print/Ability entries',
    parameters: { layout: 'padded' },
};

export const Entries = () =>
    fixtureExtract('/print/skill-tree.html', '.ability', {
        limit: 6,
        gap: '26px 40px',
        onItem: (n) => { n.style.width = '300px'; },
    });

export const CharacteristicBadges = () =>
    fixtureExtract('/print/skill-tree.html', '.char-badge', {
        gap: '12px',
        onItem: (n) => { n.style.transform = 'scale(1.6)'; n.style.margin = '10px'; },
    });
