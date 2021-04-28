import '../css/radio-table-settings.css';

import { TabbedUI } from './src/TabbedUI.js';
import { RadioTableColumnsUI } from './src/RadioTableColumnsUI.js';

function setupCustomWidthInput()
{
    let widthTypeInput = document.querySelector('.radio-table-width-type');
    let customWidthFieldWrapper = document.querySelector('.radio-table-custom-width-wrapper');

    const WIDTH_CUSTOM = widthTypeInput.dataset.customWidthValue;

    let updateFieldVisibility = () => {
        customWidthFieldWrapper.hidden = (widthTypeInput.value != WIDTH_CUSTOM);
    };

    updateFieldVisibility();
    widthTypeInput.addEventListener('change', updateFieldVisibility);
    widthTypeInput.addEventListener('blur', updateFieldVisibility);
}

document.addEventListener('DOMContentLoaded', () => {
    new TabbedUI(document.querySelector('.tabbed-ui'));
    new RadioTableColumnsUI(document.querySelector('.radio-table-columns'));

    setupCustomWidthInput();
});
