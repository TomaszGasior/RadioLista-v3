import '../css/radio-station-edit-add.css';

import { TabbedUI } from './src/TabbedUI.js';

function setupLocalityInput()
{
    let localityTypeInput = document.querySelector('.locality-type-input');
    let localityCityWrapper = document.querySelector('.locality-city-wrapper');

    const LOCALITY_COUNTRY = localityTypeInput.dataset.localityCountryValue;

    let updateFieldVisibility = () => {
        localityCityWrapper.hidden = (localityTypeInput.value == LOCALITY_COUNTRY);
    };

    updateFieldVisibility();
    localityTypeInput.addEventListener('change', updateFieldVisibility);
    localityTypeInput.addEventListener('blur', updateFieldVisibility);
}

document.addEventListener('DOMContentLoaded', () => {
    new TabbedUI(document.querySelector('.tabbed-ui'));

    setupLocalityInput();
});
