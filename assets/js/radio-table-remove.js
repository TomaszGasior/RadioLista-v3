import '../css/radio-table-remove.css';

function setupRemoveConfirmDialog()
{
    let input = document.querySelector('.radio-table-remove-confirm');

    let confirmDialog = () => {
        if (input.checked && false === window.confirm(input.dataset.confirmMessage)) {
            input.checked = false;
            input.blur();
        }

        input.removeEventListener('change', confirmDialog);
    };

    input.addEventListener('change', confirmDialog);
}

document.addEventListener('DOMContentLoaded', () => {
    setupRemoveConfirmDialog();
});
