export class NumberIndentManager
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
