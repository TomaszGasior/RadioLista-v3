import '../../css/common/sortable-table.css';

export class SortableTable
{
    constructor(table)
    {
        this.headers = table.querySelectorAll('thead th');
        this.body = table.querySelector('tbody');
        this.rows = [...this.body.querySelectorAll('tr')];

        this.order = 1; // 1 = ascending, -1 = descending
        this.lastColumnIndex = -1;

        this.setupMarkup();
    }

    setupMarkup()
    {
        this.rows.forEach(function(row){
            // It's needed for comparing equal values.
            row.dataset.index = row.rowIndex;
        });

        this.headers.forEach(header => {
            if (header.dataset.sort) {
                header.addEventListener('click', this.onHeaderClick.bind(this));
                header.addEventListener('keydown', this.onHeaderKeydown.bind(this));
                header.role = 'button';
                header.tabIndex = 0;
            }
        });
    }

    onHeaderClick(event)
    {
        let header = event.target;
        let columnIndex = header.cellIndex;
        let sortingType = header.dataset.sort;

        header.blur();

        this.order = (columnIndex === this.lastColumnIndex) ? (this.order * -1) : 1;
        this.lastColumnIndex = columnIndex;

        this.rows.sort(this.compareRows.bind(this, columnIndex, sortingType));
        this.rows.forEach(row => { this.body.appendChild(row); });

        this.headers.forEach(header => {
            header.classList.remove('sorted-asc');
            header.classList.remove('sorted-desc');
        });
        header.classList.add(this.order == 1 ? 'sorted-asc' : 'sorted-desc');
    }

    onHeaderKeydown(event)
    {
        const ENTER_KEY = 13;
        const SPACE_KEY = 32;

        if (event.keyCode != ENTER_KEY && event.keyCode != SPACE_KEY) {
            return;
        }

        let header = event.target;

        event.preventDefault();
        header.dispatchEvent(new Event('click'));
        header.focus();
    }

    compareRows(columnIndex, sortingType, row1, row2)
    {
        // Use textContent to ignore HTML in row content. Trim HTML indentation.
        let value1 = row1.children[columnIndex].textContent.trim();
        let value2 = row2.children[columnIndex].textContent.trim();

        if ('N' === sortingType) {
            if('' !== value1) {
                value1 = Number.parseFloat(value1.replace(',', '.'));
            }
            if('' !== value2) {
                value2 = Number.parseFloat(value2.replace(',','.'));
            }
        }

        if (value1 === value2) {
            return row1.dataset.index - row2.dataset.index;
        }
        if ('' === value1) {
            return 1;
        }
        if ('' === value2) {
            return -1;
        }

        let result;
        if ('T' === sortingType) {
            result = value1.localeCompare(value2, [], {usage: 'sort'});
        }
        else {
            result = (value1 > value2 ? 1 : -1);
        }

        return result * this.order;
    }
}
