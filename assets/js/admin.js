import '../css/admin.css';

import { SortableTable } from './src/SortableTable.js';

document.addEventListener('DOMContentLoaded', () => {
    let table = document.querySelector('table.sortable');

    if (table) {
        new SortableTable(table);
    }
});
