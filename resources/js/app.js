import './bootstrap';

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-password-toggle]');

    if (!toggle) {
        return;
    }

    const input = document.getElementById(toggle.dataset.passwordToggle ?? '');

    if (!input) {
        return;
    }

    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';
    toggle.textContent = isHidden ? 'Hide' : 'Show';
    toggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
});
