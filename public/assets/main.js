(function(){
    function SortableTable(table)
    {
        this.headers = Array.from(table.querySelectorAll('thead th'));
        this.body = table.querySelector('tbody');
        this.rows = Array.from(this.body.querySelectorAll('tr'));

        this.order = 1; // 1 = ascending, -1 = descending
        this.lastColumnIndex = -1;

        this.compareRows = function(columnIndex, sortingType, row1, row2)
        {
            var value1 = row1.children[columnIndex].textContent;
            var value2 = row2.children[columnIndex].textContent;

            if ('N' === sortingType) {
                if('' !== value1) {
                    value1 = parseFloat(value1.replace(',', '.'));
                }
                if('' !== value2) {
                    value2 = parseFloat(value2.replace(',','.'));
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

            if ('T' === sortingType) {
                var result = value1.localeCompare(value2, [], {usage: 'sort'});
            }
            else {
                var result = (value1 > value2 ? 1 : -1);
            }

            return result * this.order;
        };
        this.onHeaderClick = function(event)
        {
            var header = event.target;
            var columnIndex = header.cellIndex;
            var sortingType = header.dataset.sort;

            header.blur();

            this.order = (columnIndex === this.lastColumnIndex) ? (this.order * -1) : 1;
            this.lastColumnIndex = columnIndex;

            this.rows.sort(this.compareRows.bind(this, columnIndex, sortingType));
            this.rows.forEach(function(row){
                this.body.appendChild(row);
            }.bind(this));

            this.headers.forEach(function(header){
                header.classList.remove('sorted-asc');
                header.classList.remove('sorted-desc');
            });
            header.classList.add(this.order == 1 ? 'sorted-asc' : 'sorted-desc');
        };
        this.onHeaderKeydown = function(event)
        {
            const ENTER_KEY = 13;
            const SPACE_KEY = 32;

            if (event.keyCode != ENTER_KEY && event.keyCode != SPACE_KEY) {
                return;
            }

            var header = event.target;

            event.preventDefault();
            header.dispatchEvent(new Event('click'));
            header.focus();
        };
        this.setupMarkup = function()
        {
            this.rows.forEach(function(row){
                // It's needed for comparing equal values.
                row.dataset.index = row.rowIndex;
            });

            this.headers.forEach(function(header){
                if (header.dataset.sort) {
                    header.addEventListener('click', this.onHeaderClick.bind(this));
                    header.addEventListener('keydown', this.onHeaderKeydown);
                    header.role = 'button';
                    header.tabIndex = 0;
                }
            }.bind(this));
        };

        this.setupMarkup();
    }

    function TabbedUI(container)
    {
        this.container = container;
        this.panels = Array.from(container.children);
        this.navigator = null;
        this.buttons = [];

        this.sessionKey = 'tabbed-ui__' + location.pathname;

        this.changePanel = function(panelNumber)
        {
            this.panels.forEach(function(panel, i){
                panel.classList.remove('tabbed-ui-current');
                this.buttons[i].classList.remove('tabbed-ui-current');

                if (i == panelNumber) {
                    panel.classList.add('tabbed-ui-current');
                    this.buttons[i].classList.add('tabbed-ui-current');
                }
            }.bind(this));
        };
        this.onNavigatorButtonClick = function(event)
        {
            event.preventDefault();

            var button = event.target;
            var panelNumber = button.dataset.panelNumber;

            button.blur();

            this.changePanel(panelNumber);
            sessionStorage.setItem(this.sessionKey, panelNumber);
        };
        this.setupNavigator = function()
        {
            this.navigator = document.createElement('ul');
            this.navigator.classList.add('tabbed-ui-navigator');
            this.navigator.setAttribute('role', 'tablist');

            this.panels.forEach(function(panel, i){
                var title = panel.querySelector('h2').innerHTML;
                var item = document.createElement('li');
                var button = document.createElement('button');

                button.innerHTML = title;
                button.dataset.panelNumber = i;
                button.type = 'button';
                button.addEventListener('click', this.onNavigatorButtonClick.bind(this));

                this.buttons[i] = button;

                item.appendChild(button);
                this.navigator.appendChild(item);
            }.bind(this));

            this.container.insertBefore(this.navigator, this.panels[0]);
        };
        this.setupPanels = function()
        {
            var defaultPanelNumber = 0;

            var currentHash = location.hash.replace('#', '');
            var savedNumber = sessionStorage.getItem(this.sessionKey);

            if (null != savedNumber) {
                defaultPanelNumber = savedNumber;
            }

            this.panels.forEach(function(panel, i){
                panel.setAttribute('role', 'tab');

                if (currentHash && panel.id == currentHash) {
                    defaultPanelNumber = i;
                }
            });

            this.changePanel(defaultPanelNumber);
        };

        this.setupNavigator();
        this.setupPanels();
    }

    document.documentElement.classList.add('JS');

    document.addEventListener('DOMContentLoaded', function(){
        var sortableTable = document.querySelector('table.sortable');
        var tabbedUI = document.querySelector('.tabbed-ui');
        var notification = document.querySelector('.notification-wrapper');

        if (sortableTable) {
            new SortableTable(sortableTable);
        }
        if (tabbedUI) {
            new TabbedUI(tabbedUI);
        }

        if (notification) {
            setTimeout(function(){
                notification.classList.add('hidden');
            }, 10000);
        }
    });
})();
