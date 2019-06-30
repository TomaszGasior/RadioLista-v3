(function(){
    function RDSPopupManager(container)
    {
        this.container = container;
        this.items = container.querySelectorAll('.rds-popup-enabled');

        this.popup = null;
        this.rowPS = null;
        this.rowRT = null;
        this.rowPTY = null;
        this.valuePS = null;
        this.valueRT = null;
        this.valuePTY = null;

        this.itemHasFocus = false;

        this.onItemMouseover = function(event)
        {
            var item = event.target;

            if (!this.itemHasFocus) {
                this.showPopupForItem(item);
            }
        };
        this.onItemFocus = function(event)
        {
            var item = event.target;

            this.itemHasFocus = true;
            this.showPopupForItem(item);
        };
        this.onItemMouseleave = function(event)
        {
            var item = event.target;

            if (!this.itemHasFocus) {
                this.hidePopup();
            }

            if (item === document.activeElement) {
                item.blur();
            }
        }
        this.onItemBlur = function()
        {
            this.itemHasFocus = false;
            this.hidePopup();
        };
        this.hasItemExtraInfo = function(item)
        {
            var PS = JSON.parse(item.dataset.ps);
            var RT = JSON.parse(item.dataset.rt);
            var PTY = JSON.parse(item.dataset.pty);

            // Don't show RDS popup if data is not specified or PS has only one frame.
            if ((PS[0] && PS[0].length < 2) && PS.length < 2 && 0 === RT.length && !PTY) {
                return false;
            }

            return true;
        };
        this.showPopupForItem = function(item)
        {
            var PS = JSON.parse(item.dataset.ps);
            var RT = JSON.parse(item.dataset.rt);
            var PTY = JSON.parse(item.dataset.pty);

            if (PS.length > 0) {
                this.rowPS.hidden = false;
                PS.forEach(function(group, key){
                   PS[key] = '<span>' + group.join('</span> <span>') + '</span>';
                });
                this.valuePS.innerHTML =
                   '<div>' + PS.join('</div> <div>') + '</div>';
            }
            if (RT.length > 0) {
                this.rowRT.hidden = false;
                this.valueRT.innerHTML =
                    '<div><span>' + RT.join('</span></div> <div><span>') + '</span></div>';
            }
            if (PTY) {
                this.rowPTY.hidden = false;
                this.valuePTY.innerHTML =
                    '<span>' + PTY + '</span>';
            }

            if (PS || RT || PTY) {
                this.popup.hidden = false;
            }

            this.updatePopupPositionForItem(item);
        };
        this.updatePopupPositionForItem = function(item)
        {
            var rect = item.getBoundingClientRect();

            this.popup.style.top = rect.top + 'px';
            this.popup.style.left = (rect.left - this.popup.clientWidth - 3) + 'px';

            var pxOutOfScreen = (rect.top + this.popup.clientHeight) - window.innerHeight + 10;
            if (pxOutOfScreen > 0) {
                this.popup.style.top = (rect.top - pxOutOfScreen) + 'px';
            }
        };
        this.hidePopup = function()
        {
            this.rowPS.hidden = true;
            this.rowRT.hidden = true;
            this.rowPTY.hidden = true;

            this.popup.hidden = true;
        };
        this.setupPopup = function()
        {
            this.popup = document.importNode(
                this.container.querySelector('.rds-popup-template').content, true
            ).children[0];

            this.rowPS = this.popup.querySelector('.rds-popup-ps-row');
            this.rowRT = this.popup.querySelector('.rds-popup-rt-row');
            this.rowPTY = this.popup.querySelector('.rds-popup-pty-row');

            this.valuePS = this.popup.querySelector('.rds-popup-ps-value');
            this.valueRT = this.popup.querySelector('.rds-popup-rt-value');
            this.valuePTY = this.popup.querySelector('.rds-popup-pty-value');

            this.popup.style.position = 'fixed';
            this.hidePopup();
            container.appendChild(this.popup);

            window.addEventListener('wheel', this.hidePopup.bind(this));
        };
        this.setupItems = function()
        {
            this.items.forEach(function(item){
                if (this.hasItemExtraInfo(item)) {
                    item.tabIndex = 0;

                    item.addEventListener('mouseover', this.onItemMouseover.bind(this));
                    item.addEventListener('mouseleave', this.onItemMouseleave.bind(this));
                    item.addEventListener('focus', this.onItemFocus.bind(this));
                    item.addEventListener('blur', this.onItemBlur.bind(this));
                }
            }.bind(this));
        };

        this.setupPopup();
        this.setupItems();
    }

    function NumberIndent(container)
    {
        this.container = container;
        this.itemsGroups = new Map;

        this.getIntegerFromItem = function(item)
        {
            return parseInt(item.textContent);
        };
        this.getDigitsCountInInteger = function(number)
        {
            return number.toString().length;
        };
        this.findMaxNumberInItems = function(items)
        {
            var numbers = items.map(function(item){
                return this.getIntegerFromItem(item);
            }.bind(this));

            return numbers.reduce(function(prev, value){
                return (prev > value ? prev : value);
            });
        };
        this.setupItemsGroups = function()
        {
            this.container.querySelectorAll('[data-number-indent]').forEach(function(item){
                var groupName = item.dataset.numberIndent;

                if (!this.itemsGroups.has(groupName)) {
                    this.itemsGroups.set(groupName, []);
                }

                this.itemsGroups.get(groupName).push(item);
            }.bind(this));
        };
        this.applyNumberIndent = function()
        {
            this.itemsGroups.forEach(function(items){
                var maxNumber = this.findMaxNumberInItems(items);
                var maxDigits = this.getDigitsCountInInteger(maxNumber);

                items.forEach(function(item){
                    var number = this.getIntegerFromItem(item);
                    var digits = this.getDigitsCountInInteger(number);
                    var digitsDifference = maxDigits - digits;

                    if (digitsDifference) {
                        item.classList.add('number-indent-' + digitsDifference);
                    }
                }.bind(this));
            }.bind(this));
        };

        this.setupItemsGroups();
        this.applyNumberIndent();
    }

    function OverflowStyles(container)
    {
        this.container = container;

        this.refreshStatus = function()
        {
            this.container.classList.remove('overflow-left');
            this.container.classList.remove('overflow-right');

            var isScrollbarVisible = (this.container.scrollWidth !== this.container.clientWidth);

            if (isScrollbarVisible) {
                if(this.container.scrollLeft > 3) {
                    this.container.classList.add('overflow-left');
                }
                if((this.container.scrollWidth - this.container.clientWidth - this.container.scrollLeft) > 3) {
                    this.container.classList.add('overflow-right');
                }
            }
        }

        this.refreshStatus();

        window.addEventListener('resize', this.refreshStatus.bind(this));
        this.container.addEventListener('scroll', this.refreshStatus.bind(this));
    }

    document.addEventListener('DOMContentLoaded', function(){
        var radioTableContainer = document.querySelector('.radiotable-container');

        if (radioTableContainer) {
            new NumberIndent(radioTableContainer);
            new OverflowStyles(radioTableContainer);
            new RDSPopupManager(radioTableContainer);
        }
    });
})();
