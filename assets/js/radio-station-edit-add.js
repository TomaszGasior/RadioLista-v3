import '../css/radio-station-edit-add.css';

import { TabbedUI } from './src/TabbedUI.js';

function setupFrequencyWithDabChannelSync()
{
    let dabChannelInput = document.querySelector('.dab-channel-input');
    let frequencyInput = document.querySelector('.frequency-input');

    const DAB_CHANNEL_FREQUENCIES = JSON.parse(dabChannelInput.dataset.dabChannelFrequencies);

    dabChannelInput.addEventListener('change', () => {
        let frequencyOfCurrentDabChannel = DAB_CHANNEL_FREQUENCIES[dabChannelInput.value];

        if (frequencyOfCurrentDabChannel !== undefined) {
            frequencyInput.value = frequencyOfCurrentDabChannel;
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    new TabbedUI(document.querySelector('.tabbed-ui'));

    setupFrequencyWithDabChannelSync();
});
