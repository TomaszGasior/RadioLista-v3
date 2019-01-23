(function(){
    function TabbedUI(tabbedUIContainer)
    {
        if (!tabbedUIContainer) {
            return;
        }

        this.container = tabbedUIContainer;
        this.panels = Array.from(tabbedUIContainer.children);
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
                var title = panel.querySelector('h3').innerHTML;
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
        }

        this.setupNavigator();
        this.setupPanels();
    }

    function Notification(notification)
    {
        if (!notification) {
            return;
        }

        setTimeout(function(){
            notification.classList.add('hidden');
        }, 10000);
    }

    document.addEventListener('DOMContentLoaded', function(){
        document.documentElement.classList.add('JS');

        new TabbedUI(document.querySelector('.tabbed-ui'));
        new Notification(document.querySelector('.notification-wrapper'));
    });
})();
