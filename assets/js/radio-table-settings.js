import { RadioTableColumnsUI } from './src/RadioTableColumnsUI.js';

document.addEventListener('DOMContentLoaded', () => {
    let container = document.querySelector('.radio-table-columns');

    if (container) {
        new RadioTableColumnsUI(container);
    }
});
