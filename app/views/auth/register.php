<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-6 mx-auto">
        <div class="card-body p-5">
            <h2 class="text-center mb-4">Create your account</h2>
            <p class="text-center mb-4">Sign up to get started</p>

            <form action="<?php echo URL_ROOT; ?>/auth/register" method="post">
                <div class="mb-4">
                    <input type="text" name="username" id="username" class="form-control form-control-lg <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="username" value="<?php echo $data['username']; ?>">
                    <?php if (!empty($data['username_err'])): ?>
                        <div class="invalid-feedback ps-3"><?php echo $data['username_err']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <input type="email" name="email" id="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="email address" value="<?php echo $data['email']; ?>">
                    <?php if (!empty($data['email_err'])): ?>
                        <div class="invalid-feedback ps-3"><?php echo $data['email_err']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-2">
                    <input type="password" name="password" id="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="password" value="<?php echo $data['password']; ?>">
                    <?php if (!empty($data['password_err'])): ?>
                        <div class="invalid-feedback ps-3"><?php echo $data['password_err']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="d-grid gap-2 d-flex justify-content-end me-2 mb-4">
                    <label class="form-check-label" for="showPassword">
                        show password
                    </label>
                    <input class="form-check-input" type="checkbox" value="" id="showPassword">
                </div>
                <div class="mb-2">
                    <input type="password" name="confirmPassword" id="confirmPassword" class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="confirm password" value="<?php echo $data['confirm_password']; ?>">
                    <?php if (!empty($data['confirm_password_err'])): ?>
                        <div class="invalid-feedback ps-3"><?php echo $data['confirm_password_err']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="d-grid gap-2 d-flex justify-content-end me-2 mb-4">
                    <label class="form-check-label" for="showConfirmPassword">
                        show confirm password
                    </label>
                    <input class="form-check-input" type="checkbox" value="" id="showConfirmPassword">
                </div>
                <button class="btn btn-primary rounded-pill d-grid col-8 mx-auto" type="submit">Enter</button>
                <div class="text-center mt-4">
                    <p class="mb-0">Already have an account? <a href="<?php echo URL_ROOT; ?>/auth/login" class="text-primary">Log in</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>