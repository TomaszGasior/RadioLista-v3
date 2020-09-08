import '../css/themes.css';

import { NumberIndentManager } from './src/NumberIndentManager.js';
import { OverflowStylesManager } from './src/OverflowStylesManager.js';
import { RDSPopupManager } from './src/RDSPopupManager.js';

document.addEventListener('DOMContentLoaded', () => {
    let container = document.querySelector('.radio-table-container');

    if (container) {
        new NumberIndentManager(container);
        new OverflowStylesManager(container);
        new RDSPopupManager(container);
    }
});
