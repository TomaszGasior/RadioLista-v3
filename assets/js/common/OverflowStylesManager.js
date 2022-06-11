export class OverflowStylesManager
{
    constructor(container)
    {
        this.container = container;

        this.container.addEventListener('scroll', this.refreshStatus.bind(this));
        window.addEventListener('resize', this.refreshStatus.bind(this));
        window.addEventListener('load', this.refreshStatus.bind(this));
    }

    refreshStatus()
    {
        const MIN_SCROLL_MARGIN = 3;

        let container = this.container;
        let isScrollbarVisible = (container.scrollWidth !== container.clientWidth);

        container.classList.remove('overflow-left');
        container.classList.remove('overflow-right');

        if (isScrollbarVisible) {
            if (container.scrollLeft > MIN_SCROLL_MARGIN) {
                container.classList.add('overflow-left');
            }
            if ((container.scrollWidth - container.clientWidth - container.scrollLeft) > MIN_SCROLL_MARGIN) {
                container.classList.add('overflow-right');
            }
        }
    }
}
