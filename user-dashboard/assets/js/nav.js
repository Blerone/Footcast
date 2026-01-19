document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('.dashboard-nav');
    const toggle = document.querySelector('.dashboard-nav-toggle');

    if (!nav || !toggle) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
});
