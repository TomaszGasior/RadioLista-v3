import { ColorSchemeHandler } from './common/ColorSchemeHandler.js';

document.addEventListener('DOMContentLoaded', () => {
    new ColorSchemeHandler(document.querySelector('.site-footer'));
});
