import '../../css/part/tabbed-ui.css';

export class TabbedUI
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
