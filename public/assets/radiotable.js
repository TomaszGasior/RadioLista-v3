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
            var PS = item.dataset.ps;
            var RT = item.dataset.rt;
            var PTY = item.dataset.pty;

            // Don't show RDS popup if data is not specified or PS has only one frame.
            if (-1 === PS.indexOf('|') && !RT && !PTY) {
                return false;
            }

            return true;
        };
        this.showPopupForItem = function(item)
        {
            var PS = item.dataset.ps;
            var RT = item.dataset.rt;
            var PTY = item.dataset.pty;

            if (PS) {
                this.rowPS.hidden = false;
                this.valuePS.innerHTML =
                    '<span>' + PS.replace(/\|/g, '</span> <span>') + '</span>';
            }
            if (RT) {
                this.rowRT.hidden = false;
                this.valueRT.innerHTML =
                    '<div><span>' + RT.replace(/\|/g, '</span></div> <div><span>') + '</span></div>';
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
        this.elementsIndent1 = container.querySelectorAll('.number-indent-1');
        this.elementsIndent2 = container.querySelectorAll('.number-indent-2');

        this.elementsIndent1.forEach(function(element){
            var value = parseInt(element.textContent);

            if (value < 10) {
                element.classList.add('number-indent-apply-1');
            }
        });
        this.elementsIndent2.forEach(function(element){
            var value = parseInt(element.textContent);

            if (value < 10) {
                element.classList.add('number-indent-apply-2');
            }
            else if (value < 100) {
                element.classList.add('number-indent-apply-1');
            }
        });
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
