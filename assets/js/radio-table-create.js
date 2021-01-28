import '../css/radio-table-create.css';

import { TabbedUI } from './src/TabbedUI.js';

document.addEventListener('DOMContentLoaded', () => {
    new TabbedUI(document.querySelector('.tabbed-ui'));
});
