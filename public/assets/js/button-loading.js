/**
 * Displays a loading state on the control used to submit a form.
 *
 * The delegated submit listener also covers forms added to the page later,
 * so no per-form initialization is required.
 */
(function () {
    'use strict';

    const loadingClass = 'is-loading';
    const spinner = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>';

    function getSubmitControl(form, submitter) {
        if (submitter) {
            return submitter;
        }

        return Array.from(form.elements).find((element) =>
            (element.tagName === 'BUTTON' && (element.type === 'submit' || !element.hasAttribute('type'))) ||
            (element.tagName === 'INPUT' && (element.type === 'submit' || element.type === 'image'))
        );
    }

    function setLoading(control) {
        if (!control || control.classList.contains(loadingClass)) {
            return;
        }

        control.classList.add(loadingClass);
        // Do not set `disabled` here: disabled submit controls are omitted from
        // the request, including their `name` values used by the PHP handlers.
        control.classList.add('pointer-events-none', 'opacity-75');
        control.setAttribute('aria-busy', 'true');
        control.setAttribute('aria-disabled', 'true');

        if (control.tagName === 'INPUT') {
            control.dataset.loadingLabel = control.value;
            control.value = 'Loading...';
            return;
        }

        control.dataset.loadingLabel = control.innerHTML;
        control.innerHTML = `${spinner}<span class="ml-2">Loading...</span>`;
    }

    function resetLoading(control) {
        if (!control || !control.classList.contains(loadingClass)) {
            return;
        }

        control.classList.remove(loadingClass);
        control.classList.remove('pointer-events-none', 'opacity-75');
        control.removeAttribute('aria-busy');
        control.removeAttribute('aria-disabled');

        if (control.tagName === 'INPUT') {
            control.value = control.dataset.loadingLabel || control.value;
        } else {
            control.innerHTML = control.dataset.loadingLabel || control.innerHTML;
        }

        delete control.dataset.loadingLabel;
    }

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.disableLoading === 'true') {
            return;
        }

        if (form.dataset.isSubmitting === 'true') {
            event.preventDefault();
            return;
        }

        form.dataset.isSubmitting = 'true';
        setLoading(getSubmitControl(form, event.submitter));
    }, true);

    // Restore buttons when returning to a page that was kept in the back/forward cache.
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('form').forEach((form) => {
            delete form.dataset.isSubmitting;
        });
        document.querySelectorAll(`.${loadingClass}`).forEach(resetLoading);
    });

    window.FormButtonLoading = { setLoading, resetLoading };
}());
