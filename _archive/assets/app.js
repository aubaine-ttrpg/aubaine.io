import { startStimulusApp } from '@symfony/stimulus-bridge';
import 'tom-select/dist/css/tom-select.css';
import './styles/app.css';
import './styles/skill-plate.css';
import './styles/skill-grid.css';
import './styles/skill-tree.css';

const app = startStimulusApp(
    require.context(
        '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
        true,
        /\.[jt]s$/
    )
);
