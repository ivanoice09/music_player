// simple check if logged in
export function checkAuth() {
    return authStatus.isLoggedIn;
}

// Show modal if authentication is required
export function showAuthRequiredModal() {
    // Check if modal already exists
    if ($('#authRequiredModal').length) {
        $('#authRequiredModal').modal('show');
        return;
    }

    // Load modal via AJAX if not in DOM
    $.get('partials/authModal', function (html) {
        $('body').append(html);
        $('#authRequiredModal').modal('show');

        // Remove modal from DOM when hidden
        $('#authRequiredModal').on('hidden.bs.modal', function () {
            $(this).remove();
        });
    }).fail(function () {
        // Fallback if AJAX fails
        alert('Please sign in to access this feature.');
        window.location.href = authStatus.loginUrl;
    });
}

// Show password visibility in login and register pages
export function showPasswordToggler(password, showPassword) {
    const passwordInput = document.getElementById(password);
    const showPasswordCheckbox = document.getElementById(showPassword);
    
    if (!passwordInput || !showPasswordCheckbox) {
        console.error('Password field or checkbox not found');
        return;
    }
    
    showPasswordCheckbox.addEventListener('change', function() {
        if (this.checked) {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    });
}