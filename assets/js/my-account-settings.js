import '../css/my-account-settings.css';

import { TabbedUI } from './src/TabbedUI.js';

document.addEventListener('DOMContentLoaded', () => {
    new TabbedUI(document.querySelector('.tabbed-ui'));
});
