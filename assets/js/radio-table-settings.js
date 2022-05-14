import '../css/radio-table-settings.css';

import Huebee from 'huebee';

function setupColorInputs()
{
    let colorInputs = document.querySelectorAll('.radio-table-appearance-color');

    // Override internal function for better visual alignment.
    Huebee.prototype.getGrayCount = function() {
        return this.options.shades;
    };

    colorInputs.forEach(input => {
        let huebee = new Huebee(input, {
            hues: 25,
            shades: 20,
            saturations: 1,
            notation: 'hex',
        });

        huebee.on('change', () => {
            huebee.close();
        })

        input.addEventListener('input', () => {
            if ('' === input.value.trim()) {
                input.removeAttribute('style');
            }
        });
    });
}

function setupCustomWidthInput()
{
    let widthTypeInput = document.querySelector('.radio-table-width-type');
    let customWidthField = document.querySelector('.radio-table-custom-width');

    const WIDTH_CUSTOM = widthTypeInput.dataset.customWidthValue;

    let updateFieldVisibility = () => {
        customWidthField.disabled = (widthTypeInput.value != WIDTH_CUSTOM);
    };

    updateFieldVisibility();
    widthTypeInput.addEventListener('change', updateFieldVisibility);
    widthTypeInput.addEventListener('blur', updateFieldVisibility);
}

function setupRemoveButtonAndDialog()
{
    let anchor = document.querySelector('.remove-radio-table-button');
    let dialog = document.querySelector('.remove-radio-table-dialog');

    let button = document.createElement('button');
    button.innerHTML = anchor.innerHTML;
    button.type = 'button';
    button.classList.add('remove-radio-table-button');

    anchor.parentNode.replaceChild(button, anchor);

    button.addEventListener('click', () => {
        dialog.showModal();
    })
}

document.addEventListener('DOMContentLoaded', () => {
    setupColorInputs();
    setupCustomWidthInput();
    setupRemoveButtonAndDialog();
});
