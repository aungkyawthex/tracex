document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.passwordToggle);
            if (!input) return;

            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';

            const showIcon = btn.querySelector('.toggle-show');
            const hideIcon = btn.querySelector('.toggle-hide');
            if (showIcon) showIcon.style.display = show ? 'none' : '';
            if (hideIcon) hideIcon.style.display = show ? '' : 'none';

            btn.setAttribute('aria-pressed', String(show));
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });
});
