export class RemoveDialogManager
{
    constructor(container)
    {
        this.anchor = container.querySelector('.remove-button');
        this.dialog = container.querySelector('.remove-dialog');

        this.setupButtonInPlaceOfAnchor();
    }

    setupButtonInPlaceOfAnchor()
    {
        // Use non-JS fallback if browser does not support dialog HTML tag.
        if ('undefined' === typeof this.dialog.showModal) {
            return;
        }

        this.button = document.createElement('button');

        this.button.innerHTML = this.anchor.innerHTML;
        this.button.className = this.anchor.className;
        this.button.type = 'button';

        this.anchor.parentNode.replaceChild(this.button, this.anchor);

        this.button.addEventListener('click', this.onButtonClick.bind(this))
    }

    onButtonClick()
    {
        this.button.blur();
        this.dialog.showModal();
    }
}
