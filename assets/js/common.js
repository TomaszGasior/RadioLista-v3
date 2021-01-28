import '../css/common.css';

document.documentElement.classList.add('JS');

document.addEventListener('DOMContentLoaded', () => {
    let notification = document.querySelector('.notification-wrapper');

    if (notification) {
        setTimeout(() => { notification.classList.add('hidden'); }, 10000);
    }
});
