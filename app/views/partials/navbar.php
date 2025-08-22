<nav class="navbar navbar-expand-lg bg-black sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="<?php echo URL_ROOT; ?>/" id="homeLink"><?php echo SITE_NAME; ?></a>
        <ul class="navbar-nav mx-md-auto">
            <?php include APP_ROOT . '/app/views/partials/searchBar.php'; ?>
        </ul>

        <ul class="navbar-nav ml-md-auto">
            <?php if (isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href=""><?php echo $_SESSION['username']; ?></a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo URL_ROOT; ?>/auth/login">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>