// Enforce leading zeros in typed values ("87.6" -> "87.60").
// Do not use "input" event. It looks better but makes manual typing hard.
// Works only in Webkit/Blink. "input.valueAsNumber" condition needed for MSEdge.
// See: https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/6367416/
function setupDecimalInputs()
{
    let decimalInputs = document.querySelectorAll('input.decimal');

    decimalInputs.forEach(input => {
        let decimals = (input.step.match(/\..*/) || [''])[0].length - 1;

        if (decimals && input.valueAsNumber != undefined) {
            let updateFixed = () => {
                input.value = input.valueAsNumber.toFixed(decimals);
            };

            input.addEventListener('change', updateFixed);
            updateFixed();
        }
    });
}

// Keep original "0.01" step for values smaller than 74, otherwise use "0.05".
// This way entering usual frequencies is simpler while OIRT frequencies are
// available too. See https://github.com/TomaszGasior/RadioLista-v3/issues/35
function setupFrequencyInput()
{
    let frequencyInput = document.querySelector('.frequency-input');

    const REPLACED_STEP = '0.05';
    const ORIGINAL_STEP = frequencyInput.step;

    let updateStep = () => {
        if (frequencyInput.value >= 74) {
            if (frequencyInput.step != REPLACED_STEP) {
                frequencyInput.step = REPLACED_STEP;
                frequencyInput.min = REPLACED_STEP;
            }
        }
        else if (frequencyInput.step != ORIGINAL_STEP) {
            frequencyInput.step = ORIGINAL_STEP;
            frequencyInput.min = ORIGINAL_STEP;
        }
    };

    updateStep();
    frequencyInput.addEventListener('input', updateStep);
    frequencyInput.addEventListener('blur', updateStep);
}

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
    setupDecimalInputs();
    setupFrequencyInput();
    setupLocalityInput();
});
