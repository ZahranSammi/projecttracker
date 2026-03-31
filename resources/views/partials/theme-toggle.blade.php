@php
    $buttonClass = $buttonClass ?? 'theme-toggle';
@endphp

<button class="{{ $buttonClass }}" type="button" data-theme-toggle aria-live="polite">
    <span class="material-symbols-outlined theme-toggle-icon" data-theme-icon>dark_mode</span>
    <span class="theme-toggle-label" data-theme-label>Dark mode</span>
</button>
