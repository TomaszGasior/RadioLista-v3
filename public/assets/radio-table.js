class RDSPopupManager
{
    constructor(container)
    {
        this.container = container;
        this.items = container.querySelectorAll('.rds-popup-enabled');

        this.popup = null;
        this.rows = { PS: null, RT: null, PTY: null };
        this.values = { PS: null, RT: null, PTY: null };
        this.itemHasFocus = false;

        this.setupPopup();
        this.setupItems();
    }

    setupPopup()
    {
        this.popup = document.importNode(
            this.container.querySelector('.rds-popup-template').content, true
        ).children[0];

        this.rows.PS = this.popup.querySelector('.rds-popup-ps-row');
        this.rows.RT = this.popup.querySelector('.rds-popup-rt-row');
        this.rows.PTY = this.popup.querySelector('.rds-popup-pty-row');

        this.values.PS = this.popup.querySelector('.rds-popup-ps-value');
        this.values.RT = this.popup.querySelector('.rds-popup-rt-value');
        this.values.PTY = this.popup.querySelector('.rds-popup-pty-value');

        this.popup.style.position = 'fixed';
        this.hidePopup();
        this.container.appendChild(this.popup);

        window.addEventListener('wheel', this.hidePopup.bind(this));
    }

    setupItems()
    {
        this.items.forEach(item => {
            if (this.hasItemExtraInfo(item)) {
                item.tabIndex = 0;

                item.addEventListener('mouseover', this.onItemMouseover.bind(this));
                item.addEventListener('mouseleave', this.onItemMouseleave.bind(this));
                item.addEventListener('focus', this.onItemFocus.bind(this));
                item.addEventListener('blur', this.onItemBlur.bind(this));
            }
        });
    }

    hasItemExtraInfo(item)
    {
        let PS = JSON.parse(item.dataset.ps);
        let RT = JSON.parse(item.dataset.rt);
        let PTY = JSON.parse(item.dataset.pty);

        // Don't show RDS popup if data is not specified or PS has only one frame.
        if ((PS[0] && PS[0].length < 2) && PS.length < 2 && 0 === RT.length && !PTY) {
            return false;
        }

        return true;
    }

    showPopupForItem(item)
    {
        let PS = JSON.parse(item.dataset.ps);
        let RT = JSON.parse(item.dataset.rt);
        let PTY = JSON.parse(item.dataset.pty);

        if (PS.length > 0) {
            PS.forEach(function(group, key){
               PS[key] = `<span>${group.join('</span> <span>')}</span>`;
            });
            this.rows.PS.hidden = false;
            this.values.PS.innerHTML = `<div>${PS.join('</div> <div>')}</div>`;
        }
        if (RT.length > 0) {
            this.rows.RT.hidden = false;
            this.values.RT.innerHTML = `<div><span>${RT.join('</span></div> <div><span>')}</span></div>`;
        }
        if (PTY) {
            this.rows.PTY.hidden = false;
            this.values.PTY.innerHTML = `<div><span>${PTY}</span></div>`;
        }

        if (PS || RT || PTY) {
            this.popup.hidden = false;
        }

        this.updatePopupPositionForItem(item);
    }

    updatePopupPositionForItem(item)
    {
        let rect = item.getBoundingClientRect();

        this.popup.style.top = rect.top + 'px';
        this.popup.style.left = (rect.left - this.popup.clientWidth - 3) + 'px';

        let pxOutOfScreen = (rect.top + this.popup.clientHeight) - window.innerHeight + 10;
        if (pxOutOfScreen > 0) {
            this.popup.style.top = (rect.top - pxOutOfScreen) + 'px';
        }
    }

    hidePopup()
    {
        this.rows.PS.hidden = true;
        this.rows.RT.hidden = true;
        this.rows.PTY.hidden = true;

        this.popup.hidden = true;
    }

    onItemMouseover(event)
    {
        let item = event.target;

        if (!this.itemHasFocus) {
            this.showPopupForItem(item);
        }
    }

    onItemFocus(event)
    {
        let item = event.target;

        this.itemHasFocus = true;
        this.showPopupForItem(item);
    }

    onItemMouseleave(event)
    {
        let item = event.target;

        if (!this.itemHasFocus) {
            this.hidePopup();
        }

        if (item === document.activeElement) {
            item.blur();
        }
    }

    onItemBlur()
    {
        this.itemHasFocus = false;
        this.hidePopup();
    }
}

class NumberIndentManager
{
    constructor(container)
    {
        this.container = container;
        this.itemsGroups = new Map;

        this.setupItemsGroups();
        this.applyNumberIndent();
    }

    setupItemsGroups()
    {
        this.container.querySelectorAll('[data-number-indent]').forEach(item => {
            let indentType = item.dataset.numberIndent;

            if (!this.itemsGroups.has(indentType)) {
                this.itemsGroups.set(indentType, []);
            }

            this.itemsGroups.get(indentType).push(item);
        });
    }

    applyNumberIndent()
    {
        this.itemsGroups.forEach(items => {
            let maxNumber = this.getMaxNumberFromItems(items);
            let maxDigits = this.getDigitsCountOfInteger(maxNumber);

            items.forEach(item => {
                let number = this.getIntegerFromItem(item);
                let digits = this.getDigitsCountOfInteger(number);
                let digitsDifference = maxDigits - digits;

                if (digitsDifference) {
                    item.classList.add(`number-indent-${digitsDifference}`);
                }
            });
        });
    }

    getIntegerFromItem(item)
    {
        return Number.parseInt(item.textContent);
    }

    getDigitsCountOfInteger(number)
    {
        return number.toString().length;
    }

    getMaxNumberFromItems(items)
    {
        let numbers = items.map(item => this.getIntegerFromItem(item));

        return numbers.reduce((prev, value) => prev > value ? prev : value);
    }
}

class OverflowStylesManager
{
    constructor(container)
    {
        this.container = container;

        this.refreshStatus();

        this.container.addEventListener('scroll', this.refreshStatus.bind(this));
        window.addEventListener('resize', this.refreshStatus.bind(this));
    }

    refreshStatus()
    {
        const MIN_SCROLL_MARGIN = 3;

        let container = this.container;
        let isScrollbarVisible = (container.scrollWidth !== container.clientWidth);

        container.classList.remove('overflow-left');
        container.classList.remove('overflow-right');

        if (isScrollbarVisible) {
            if(container.scrollLeft > MIN_SCROLL_MARGIN) {
                container.classList.add('overflow-left');
            }
            if((container.scrollWidth - container.clientWidth - container.scrollLeft) > MIN_SCROLL_MARGIN) {
                container.classList.add('overflow-right');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function(){
    let container = document.querySelector('.radio-table-container');

    if (container) {
        new NumberIndentManager(container);
        new OverflowStylesManager(container);
        new RDSPopupManager(container);
    }
});
