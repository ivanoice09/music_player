<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<style>
    .form-group-inside {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .form-control-inside {
        padding: 1.375rem 1rem 0.5rem 1rem;
        height: calc(3.5rem + 2px);
        /* border-radius: 2rem !important; */
    }
    .form-label-inside {
        position: absolute;
        top: 0.65rem;
        left: 1rem;
        font-size: 0.875rem;
        color: #6c757d;
        transition: all 0.2s;
        pointer-events: none;
    }
    .form-control-inside:focus ~ .form-label-inside,
    .form-control-inside:not(:placeholder-shown) ~ .form-label-inside {
        top: 0.25rem;
        font-size: 0.75rem;
    }
    .toggle-password-inside {
        position: absolute;
        right: 1rem;
        top: 1rem;
        cursor: pointer;
        color: #6c757d;
    }
</style>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-6 mx-auto">
        
            <div class="card-body p-5">
                <h2 class="text-center mb-4">Create your account</h2>
                <p class="text-center text-muted mb-4">Sign up to get started</p>
                
                <form action="<?php echo URL_ROOT; ?>/auth/register" method="post">
                    <div class="form-group-inside">
                        <input type="text" name="username" id="username" class="form-control form-control-inside <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" 
                               placeholder=" " value="<?php echo $data['username']; ?>">
                        <label for="username" class="form-label-inside">Username</label>
                        <?php if (!empty($data['username_err'])): ?>
                            <div class="invalid-feedback ps-3"><?php echo $data['username_err']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group-inside">
                        <input type="email" name="email" id="email" class="form-control form-control-inside <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                               placeholder=" " value="<?php echo $data['email']; ?>">
                        <label for="email" class="form-label-inside">Email</label>
                        <?php if (!empty($data['email_err'])): ?>
                            <div class="invalid-feedback ps-3"><?php echo $data['email_err']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group-inside">
                        <input type="password" name="password" id="password" class="form-control form-control-inside <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                               placeholder=" " value="<?php echo $data['password']; ?>">
                        <label for="password" class="form-label-inside">Password</label>
                        <i class="far fa-eye-slash toggle-password-inside" onclick="togglePassword('password')"></i>
                        <?php if (!empty($data['password_err'])): ?>
                            <div class="invalid-feedback ps-3"><?php echo $data['password_err']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group-inside">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-inside <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" 
                               placeholder=" " value="<?php echo $data['confirm_password']; ?>">
                        <label for="confirm_password" class="form-label-inside">Confirm Password</label>
                        <i class="far fa-eye-slash toggle-password-inside" onclick="togglePassword('confirm_password')"></i>
                        <?php if (!empty($data['confirm_password_err'])): ?>
                            <div class="invalid-feedback ps-3"><?php echo $data['confirm_password_err']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 py-3 mb-3">Sign Up</button>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account? <a href="<?php echo URL_ROOT; ?>/auth/login" class="text-primary">Log in</a></p>
                    </div>
                </form>
            </div>
        
    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = input.parentElement.querySelector('.toggle-password-inside');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    }
}
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>