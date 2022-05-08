import '../css/radio-station-edit-add.css';

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

function setupRemoveButtonAndDialog()
{
    let anchor = document.querySelector('.remove-radio-station-button');
    let dialog = document.querySelector('.remove-radio-station-dialog');

    let button = document.createElement('button');
    button.innerHTML = anchor.innerHTML;
    button.type = 'button';
    button.classList.add('remove-radio-station-button');

    anchor.parentNode.replaceChild(button, anchor);

    button.addEventListener('click', () => {
        dialog.showModal();
    })
}

document.addEventListener('DOMContentLoaded', () => {
    setupFrequencyWithDabChannelSync();
    setupRemoveButtonAndDialog();
});
