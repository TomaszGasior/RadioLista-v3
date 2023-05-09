import '../../css/radio-table/settings.css';

import { RemoveDialogManager } from '../common/RemoveDialogManager.js';

function setupCustomWidthInput()
{
    let widthTypeInput = document.querySelector('.radio-table-width-type');
    let customWidthField = document.querySelector('.radio-table-custom-width');

    const WIDTH_CUSTOM = widthTypeInput.dataset.customWidthValue;

    // This is not added server-side for better UX of non-JS users.
    customWidthField.required = true;

    let updateFieldVisibility = () => {
        customWidthField.disabled = (widthTypeInput.value != WIDTH_CUSTOM);
    };

    updateFieldVisibility();
    widthTypeInput.addEventListener('change', updateFieldVisibility);
    widthTypeInput.addEventListener('blur', updateFieldVisibility);
}

document.addEventListener('DOMContentLoaded', () => {
    setupCustomWidthInput();

    new RemoveDialogManager(document);
});
