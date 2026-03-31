import './bootstrap';

const themeStorageKey = 'glacier-theme';

const applyTheme = (theme) => {
    const root = document.documentElement;
    const normalizedTheme = theme === 'dark' ? 'dark' : 'light';

    root.dataset.theme = normalizedTheme;
    root.classList.toggle('dark', normalizedTheme === 'dark');
};

const syncThemeToggles = () => {
    const isDark = document.documentElement.dataset.theme === 'dark';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const icon = button.querySelector('[data-theme-icon]');
        const label = button.querySelector('[data-theme-label]');
        const nextThemeLabel = isDark ? 'Light mode' : 'Dark mode';

        if (icon) {
            icon.textContent = isDark ? 'light_mode' : 'dark_mode';
        }

        if (label) {
            label.textContent = nextThemeLabel;
        }

        button.setAttribute('aria-label', `Switch to ${nextThemeLabel.toLowerCase()}`);
        button.setAttribute('title', `Switch to ${nextThemeLabel.toLowerCase()}`);
    });
};

applyTheme(localStorage.getItem(themeStorageKey) ?? document.documentElement.dataset.theme ?? 'light');
syncThemeToggles();

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-password-toggle]');

    if (toggle) {
        const input = document.getElementById(toggle.dataset.passwordToggle ?? '');

        if (!input) {
            return;
        }

        const isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';
        toggle.textContent = isHidden ? 'Hide' : 'Show';
        toggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        return;
    }

    const themeToggle = event.target.closest('[data-theme-toggle]');

    if (!themeToggle) {
        return;
    }

    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

    localStorage.setItem(themeStorageKey, nextTheme);
    applyTheme(nextTheme);
    syncThemeToggles();
});
