import { startStimulusApp } from '@symfony/stimulus-bridge';
import 'tom-select/dist/css/tom-select.css';
import './styles/app.css';

const app = startStimulusApp(
    require.context(
        '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
        true,
        /\.[jt]s$/
    )
);
