document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
        return;
    }

    if (form.hasAttribute('data-submit-once')) {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }
        form.dataset.submitting = 'true';
        form.querySelectorAll('button[type="submit"]').forEach((button) => {
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
        });
    }
});

window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-submit-once]').forEach((form) => {
        delete form.dataset.submitting;
        form.querySelectorAll('button[type="submit"]').forEach((button) => {
            button.disabled = false;
            button.removeAttribute('aria-busy');
        });
    });
});
