document.documentElement.classList.add('JS');

class SortableTable
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

class TabbedUI
{
    constructor(container)
    {
        this.container = container;
        this.panels = [...container.children];
        this.navigator = null;
        this.buttons = [];

        this.setupNavigator();
        this.setupPanels();
    }

    get LAST_PANEL_SESSION_KEY()
    {
        return 'tabbed-ui__' + location.pathname;
    }

    setupNavigator()
    {
        this.navigator = document.createElement('ul');
        this.navigator.classList.add('tabbed-ui-navigator');
        this.navigator.setAttribute('role', 'tablist');

        this.panels.forEach((panel, i) => {
            let title = panel.querySelector('h2').innerHTML;
            let item = document.createElement('li');
            let button = document.createElement('button');

            button.innerHTML = title;
            button.dataset.panelNumber = i;
            button.type = 'button';
            button.addEventListener('click', this.onNavigatorButtonClick.bind(this));

            this.buttons[i] = button;

            item.appendChild(button);
            this.navigator.appendChild(item);
        });

        this.container.insertBefore(this.navigator, this.panels[0]);
    }

    setupPanels()
    {
        let defaultPanelNumber = 0;

        let currentHash = location.hash.replace('#', '');
        let savedPanelNumber = sessionStorage.getItem(this.LAST_PANEL_SESSION_KEY);

        if (null != savedPanelNumber) {
            defaultPanelNumber = savedPanelNumber;
        }

        this.panels.forEach((panel, i) => {
            panel.setAttribute('role', 'tab');

            if (currentHash && panel.id == currentHash) {
                defaultPanelNumber = i;

                // If default panel was determined by URL hash, scroll to
                // the top of the page to prevent default browser behavior.
                let scrollToTop = () => {
                    window.scroll(0, 0);
                    window.removeEventListener('scroll', scrollToTop);
                };
                window.addEventListener('scroll', scrollToTop);
            }
        });

        this.changePanel(defaultPanelNumber);
    }

    changePanel(panelNumber)
    {
        this.panels.forEach((panel, i) => {
            panel.classList.remove('tabbed-ui-current');
            this.buttons[i].classList.remove('tabbed-ui-current');

            if (i == panelNumber) {
                panel.classList.add('tabbed-ui-current');
                this.buttons[i].classList.add('tabbed-ui-current');
            }
        });
    }

    onNavigatorButtonClick(event)
    {
        event.preventDefault();

        let button = event.target;
        let panelNumber = button.dataset.panelNumber;

        button.blur();

        this.changePanel(panelNumber);
        sessionStorage.setItem(this.LAST_PANEL_SESSION_KEY, panelNumber);
    }
}

document.addEventListener('DOMContentLoaded', function(){
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
