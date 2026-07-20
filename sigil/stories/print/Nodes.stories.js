import '../../../catalyst/assets/styles/print.css';
import { fixtureExtract } from '../_helpers.js';

/*
 * Skill-tree node shapes (circle=active, square=passive, octagon=evolution,
 * concave=special), each coloured by its domain and carrying its icon. Pulled
 * from the skill-tree fixture, so the shapes and colours are the real ones.
 */
export default {
    title: 'Print/Nodes',
    parameters: { layout: 'padded' },
};

export const Shapes = () =>
    fixtureExtract('/print/skill-tree.html', '.node__shape', {
        gap: '24px',
        onItem: (n) => { n.style.position = 'static'; },
    });

export const Nameplates = () =>
    fixtureExtract('/print/skill-tree.html', '.nameplate', {
        gap: '18px',
        onItem: (n) => {
            n.style.position = 'static';
            n.style.transform = 'none';
            n.style.left = 'auto';
            n.style.top = 'auto';
        },
    });
