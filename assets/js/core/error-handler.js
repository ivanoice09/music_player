export function showError(message, duration = 3000) {
    const errorElement = $(`<div class="alert alert-danger">${message}</div>`);
    $('#mainView').prepend(errorElement);
    setTimeout(() => errorElement.fadeOut(), duration);
}

export function showToast(message) {
    // Implement a simple toast notification
    const toast = $(`<div class="toast">${message}</div>`);
    $('body').append(toast);
    setTimeout(() => toast.remove(), 3000);
}