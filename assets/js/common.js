import '../css/common.css';

import { SortableTable } from './src/SortableTable.js';
import { TabbedUI } from './src/TabbedUI.js';

document.documentElement.classList.add('JS');

document.addEventListener('DOMContentLoaded', () => {
    let sortableTable = document.querySelector('table.sortable');
    let tabbedUI = document.querySelector('.tabbed-ui');
    let notification = document.querySelector('.notification-wrapper');

    if (sortableTable) {
        new SortableTable(sortableTable);
    }
    if (tabbedUI) {
        new TabbedUI(tabbedUI);
    }
    if (notification) {
        setTimeout(() => { notification.classList.add('hidden'); }, 10000);
    }
});
