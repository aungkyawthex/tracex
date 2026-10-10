// Usage: <button type="submit" name="btnSave" data-loading-text="Saving...">Save</button>
document.addEventListener('submit', (e) => {
    const button = e.submitter;
    if (e.defaultPrevented || !button?.hasAttribute('data-loading-text')) return;

    // Disabled buttons are left out of the POST data, but the server checks for the button's name.
    if (button.name) {
        const proxy = Object.assign(document.createElement('input'), { type: 'hidden', name: button.name, value: button.value });
        proxy.dataset.loadingProxy = '';
        e.target.append(proxy);
    }

    button.dataset.originalHtml = button.innerHTML;
    button.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2 self-center"></i>${button.dataset.loadingText}`;
    button.disabled = true;
    button.classList.add('opacity-75');
});

// Restore buttons when the page comes back from the back/forward cache.
window.addEventListener('pageshow', (e) => {
    if (!e.persisted) return;

    document.querySelectorAll('[data-loading-proxy]').forEach((proxy) => proxy.remove());
    document.querySelectorAll('[data-original-html]').forEach((button) => {
        button.innerHTML = button.dataset.originalHtml;
        button.disabled = false;
        button.classList.remove('opacity-75');
        delete button.dataset.originalHtml;
    });
});
