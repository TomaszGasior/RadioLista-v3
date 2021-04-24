import '../../css/part/tabbed-ui.css';
import tocbot from 'tocbot';

export class TabbedUI
{
    constructor(container)
    {
        /** @type {!Element} */
        this.container = container;

        this.wrapper = null;
        this.navigation = null;

        this.setupMarkup();
        this.setupNavigation();
    }

    setupMarkup()
    {
        this.wrapper = document.createElement('div');
        this.wrapper.classList.add('tabbed-ui-wrapper');

        this.navigation = document.createElement('nav');
        this.navigation.classList.add('tabbed-ui-navigation');

        this.container.parentNode.replaceChild(this.wrapper, this.container);
        this.wrapper.appendChild(this.navigation);
        this.wrapper.appendChild(this.container);
    }

    setupNavigation()
    {
        this.container.querySelectorAll('h2').forEach(node => {
            node.id = this.slugify(node.textContent);
        });

        tocbot.init({
            tocSelector: '.tabbed-ui-navigation',
            contentSelector: '.tabbed-ui',
            headingSelector: 'h2',

            activeLinkClass: 'active',

            basePath: window.location.pathname,
        });
    }

    slugify(string)
    {
        // https://gist.github.com/codeguy/6684588

        return string
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9 ]/g, '')
            .replace(/\s+/g, '-')
        ;
    }
}
