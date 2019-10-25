class RadioTableColumnsUI
{
    constructor(container)
    {
        this.container = container;
        this.items = [];

        this.prepareUI();
    }

    prepareUI()
    {
        let buttonsTemplate = document.importNode(
            this.container.querySelector('.radiotable-columns-buttons-template').content, true
        );
        let buttons = [
            buttonsTemplate.querySelector('button.toggle'),
            buttonsTemplate.querySelector('button.move-up'),
            buttonsTemplate.querySelector('button.move-down'),
        ];

        let blocks = this.container.querySelectorAll('.form-item');

        blocks.forEach(block => {
            let item = {
                block: block,
                input: block.querySelector('input'),
                buttonToggle: buttons[0].cloneNode(true),
                buttonMoveUp: buttons[1].cloneNode(true),
                buttonMoveDown: buttons[2].cloneNode(true),
            }

            let itemKey = Math.abs(item.input.value);
            this.items[itemKey] = item;

            item.input.type = 'hidden';

            item.buttonToggle.addEventListener('click', this.onToggleButtonClick.bind(this));
            item.buttonMoveUp.addEventListener('click', this.onMoveUpButtonClick.bind(this));
            item.buttonMoveDown.addEventListener('click', this.onMoveDownButtonClick.bind(this));

            // Items with "min" attribute represent radiotable columns which
            // always have to be shown and user isn't allowed to hide them.
            if (item.input.hasAttribute('min')) {
                item.buttonToggle.disabled = true;
            }

            let buttonsContainer = item.block.lastElementChild;
            buttonsContainer.appendChild(item.buttonToggle);
            buttonsContainer.appendChild(item.buttonMoveUp);
            buttonsContainer.appendChild(item.buttonMoveDown);

            let label = block.querySelector('label');
            label.removeAttribute('for');
            item.buttonMoveUp.setAttribute(
                'aria-label', `$(label.textContent) $(item.buttonMoveUp.textContent)`
            );
            item.buttonMoveDown.setAttribute(
                'aria-label', `$(label.textContent) $(item.buttonMoveDown.textContent)`
            );
            item.buttonToggle.dataset.label = label.textContent;
        });

        this.refreshUI();
    }

    refreshUI()
    {
        this.items.forEach((item, itemKey) => {
            let isItemVisible = item.input.value > 0;
            let isItemFirst = 1 === itemKey;
            let isItemLast = (this.items.length - 1) === itemKey;

            isItemVisible ? item.block.classList.remove('hidden-column')
                          : item.block.classList.add('hidden-column');

            item.buttonToggle.textContent = isItemVisible ? item.buttonToggle.dataset.hideLabel
                                            : item.buttonToggle.dataset.showLabel;
            item.buttonToggle.setAttribute(
                'aria-label', `${item.buttonToggle.dataset.label} ${item.buttonToggle.textContent}`
            );

            item.buttonMoveUp.disabled = isItemFirst;
            item.buttonMoveDown.disabled = isItemLast;

            item.buttonToggle.dataset.itemKey = itemKey;
            item.buttonMoveUp.dataset.itemKey = itemKey;
            item.buttonMoveDown.dataset.itemKey = itemKey;

            this.container.removeChild(item.block);
            this.container.appendChild(item.block);
        });
    }

    swapItems(firstItemKey, secondItemKey)
    {
        let firstItem = this.items[firstItemKey];
        let secondItem = this.items[secondItemKey];

        let isFirstItemVisible = firstItem.input.value > 0;
        let isSecondItemVisible = secondItem.input.value > 0;

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
    }

    onToggleButtonClick(event)
    {
        let button = event.target;
        let itemKey = Number.parseInt(button.dataset.itemKey);

        let item = this.items[itemKey];
        item.input.value *= -1;

        this.refreshUI();
        button.focus();
    }

    onMoveUpButtonClick(event)
    {
        let button = event.target;
        let itemKey = Number.parseInt(button.dataset.itemKey);

        let isItemFirst = 1 === itemKey;
        if (!isItemFirst) {
            this.swapItems(itemKey - 1, itemKey);
        }

        this.refreshUI();
        button.focus();
    }

    onMoveDownButtonClick(event)
    {
        let button = event.target;
        let itemKey = Number.parseInt(button.dataset.itemKey);

        let isItemLast = (this.items.length - 1) === itemKey;
        if (!isItemLast) {
            this.swapItems(itemKey, itemKey + 1);
        }

        this.refreshUI();
        button.focus();
    }
}

document.addEventListener('DOMContentLoaded', function(){
    let container = document.querySelector('.radiotable-columns');

    if (container) {
        new RadioTableColumnsUI(container);
    }
});
