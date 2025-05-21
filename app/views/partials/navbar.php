<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" style="height: 70px">
    <div class="container">
        <a class="navbar-brand" href="<?php echo URL_ROOT; ?>"><?php echo SITE_NAME; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a href="<?php echo URL_ROOT; ?>/music/browse" class="nav-link">Browse</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><?php echo $_SESSION['username']; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URL_ROOT; ?>/auth/logout">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URL_ROOT; ?>/auth/register">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URL_ROOT; ?>/auth/login">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
            <!-- Search form -->
            <form class="form-inline my-2 my-lg-0 mx-auto" action="<?php echo URL_ROOT; ?>/music/search" method="post">
                <input class="form-control" type="search" name="query" placeholder="Search music..." aria-label="Search" style="width: 400px;">
            </form>
        </div>
    </div>
</nav>