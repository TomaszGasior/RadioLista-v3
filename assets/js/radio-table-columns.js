import '../css/radio-table-columns.css';

import { RadioTableColumnsUI } from './src/RadioTableColumnsUI.js';

document.addEventListener('DOMContentLoaded', () => {
    new RadioTableColumnsUI(document.querySelector('.radio-table-columns'));
});
