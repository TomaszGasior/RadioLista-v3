import '../../css/radio-table/show.css';

import { NumberIndentManager } from '../common/NumberIndentManager.js';
import { OverflowStylesManager } from '../common/OverflowStylesManager.js';
import { RDSPopupManager } from '../common/RDSPopupManager.js';
import { SortableTable } from '../common/SortableTable.js';

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
