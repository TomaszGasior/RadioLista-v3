(function(){
    function RadioTableColumnsUI(container)
    {
        this.container = container;
        this.items = [];

        this.onToggleButtonClick = function(event)
        {
            var button = event.target;
            var itemKey = parseInt(button.dataset.itemKey);

            var item = this.items[itemKey];
            item.input.value *= -1;

            this.refreshUI();
            button.focus();
        };
        this.onMoveUpButtonClick = function(event)
        {
            var button = event.target;
            var itemKey = parseInt(button.dataset.itemKey);

            var isItemFirst = 1 === itemKey;
            if (!isItemFirst) {
                this.swapItems(itemKey - 1, itemKey);
            }

            this.refreshUI();
            button.focus();
        };
        this.onMoveDownButtonClick = function(event)
        {
            var button = event.target;
            var itemKey = parseInt(button.dataset.itemKey);

            var isItemLast = (this.items.length - 1) === itemKey;
            if (!isItemLast) {
                this.swapItems(itemKey, itemKey + 1);
            }

            this.refreshUI();
            button.focus();
        };
        this.swapItems = function(firstItemKey, secondItemKey)
        {
            var firstItem = this.items[firstItemKey];
            var secondItem = this.items[secondItemKey];

            var isFirstItemVisible = firstItem.input.value > 0;
            var isSecondItemVisible = secondItem.input.value > 0;

            firstItem.input.value = secondItemKey;
            secondItem.input.value = firstItemKey;

            if (!isFirstItemVisible) {
                firstItem.input.value *= -1;
            }
            if (!isSecondItemVisible) {
                secondItem.input.value *= -1;
            }

            this.items[firstItemKey] = secondItem;
            this.items[secondItemKey] = firstItem;
        };
        this.refreshUI = function()
        {
            this.items.forEach(function(item, itemKey){
                var isItemVisible = item.input.value > 0;
                var isItemFirst = 1 === itemKey;
                var isItemLast = (this.items.length - 1) === itemKey;

                isItemVisible ? item.block.classList.remove('hidden-column')
                              : item.block.classList.add('hidden-column');

                item.buttonToggle.textContent = isItemVisible ? item.buttonToggle.dataset.hideLabel
                                                : item.buttonToggle.dataset.showLabel;
                item.buttonToggle.setAttribute(
                    'aria-label', item.buttonToggle.dataset.label + ' ' + item.buttonToggle.textContent
                );

                item.buttonMoveUp.disabled = isItemFirst;
                item.buttonMoveDown.disabled = isItemLast;

                item.buttonToggle.dataset.itemKey = itemKey;
                item.buttonMoveUp.dataset.itemKey = itemKey;
                item.buttonMoveDown.dataset.itemKey = itemKey;

                this.container.removeChild(item.block);
                this.container.appendChild(item.block);
            }.bind(this));
        };
        this.prepareUI = function()
        {
            var buttonsTemplate = document.importNode(
                this.container.querySelector('.radiotable-columns-buttons-template').content, true
            );
            var buttons = [
                buttonsTemplate.querySelector('button.toggle'),
                buttonsTemplate.querySelector('button.move-up'),
                buttonsTemplate.querySelector('button.move-down'),
            ];

            var blocks = Array.from(this.container.querySelectorAll('.form-item'));

            blocks.forEach(function(buttons, block){
                var item = {
                    block: block,
                    input: block.querySelector('input'),
                    buttonToggle: buttons[0].cloneNode(true),
                    buttonMoveUp: buttons[1].cloneNode(true),
                    buttonMoveDown: buttons[2].cloneNode(true),
                };

                var itemKey = Math.abs(item.input.value);
                this.items[itemKey] = item;

                item.input.type = 'hidden';

                item.buttonToggle.addEventListener('click', this.onToggleButtonClick.bind(this));
                item.buttonMoveUp.addEventListener('click', this.onMoveUpButtonClick.bind(this));
                item.buttonMoveDown.addEventListener('click', this.onMoveDownButtonClick.bind(this));

                if (item.input.hasAttribute('min')) {
                    item.buttonToggle.disabled = true;
                }

                var buttonsContainer = item.block.lastElementChild;
                buttonsContainer.appendChild(item.buttonToggle);
                buttonsContainer.appendChild(item.buttonMoveUp);
                buttonsContainer.appendChild(item.buttonMoveDown);

                var label = block.querySelector('label');
                label.removeAttribute('for');
                item.buttonMoveUp.setAttribute(
                    'aria-label', label.textContent + ' ' + item.buttonMoveUp.textContent
                );
                item.buttonMoveDown.setAttribute(
                    'aria-label', label.textContent + ' ' + item.buttonMoveDown.textContent
                );
                item.buttonToggle.dataset.label = label.textContent;
            }.bind(this, buttons));

            this.refreshUI();
        };

        this.prepareUI();
    }

    document.addEventListener('DOMContentLoaded', function(){
        var container = document.querySelector('.radiotable-columns');

        if (container) {
            new RadioTableColumnsUI(container);
        }
    });
})();
