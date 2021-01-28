import '../css/radio-table-show.css';

import { NumberIndentManager } from './src/NumberIndentManager.js';
import { OverflowStylesManager } from './src/OverflowStylesManager.js';
import { RDSPopupManager } from './src/RDSPopupManager.js';
import { SortableTable } from './src/SortableTable.js';

document.addEventListener('DOMContentLoaded', () => {
    let container = document.querySelector('.radio-table-container');

    if (!container) {
        return;
    }

    new NumberIndentManager(container);
    new OverflowStylesManager(container);
    new RDSPopupManager(container);

    new SortableTable(container.querySelector('table.sortable'));
});
