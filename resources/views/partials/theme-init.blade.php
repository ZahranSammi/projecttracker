<script>
    (() => {
        const storageKey = 'glacier-theme';
        const root = document.documentElement;
        const preferredTheme = localStorage.getItem(storageKey)
            ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        root.dataset.theme = preferredTheme;
        root.classList.toggle('dark', preferredTheme === 'dark');
    })();
</script>
