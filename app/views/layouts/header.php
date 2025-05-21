<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/search.css">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/style.css">
</head>

<body>
    <div id="searchResultsContainer" class="container mt-4" style="display: none;">
        <div class="row" id="instantResults">
            <!-- Results will appear here -->
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo URL_ROOT; ?>"><?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
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
                <!-- Replace the search form with this -->
                <form class="form-inline my-2 my-lg-0" action="<?php echo URL_ROOT; ?>/music/search" method="post">
                    <div class="input-group">
                        <input class="form-control" type="search" name="query" placeholder="Search music..." aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </nav>
    <div class="container">
        <?php flash('register_success'); ?>