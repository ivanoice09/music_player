
<!-- app/views/partials/authModal.php -->
<div class="modal fade" id="authRequiredModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-lock me-2"></i> Authentication Required
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You need to be signed in to access this feature.</p>
                <div class="d-flex flex-column">
                    <a href="<?= URL_ROOT ?>/auth/login" class="btn btn-primary mb-2">
                        <i class="fas fa-sign-in-alt me-2"></i> Sign In
                    </a>
                    <a href="<?= URL_ROOT ?>/auth/register" class="btn btn-outline-light">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </a>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>
