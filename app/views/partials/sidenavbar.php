
<!-- NAVBAR 2.0 -->
<nav class="navbar-vertical fixed-left">
    <div class="navbar-icons">
        <!-- Profile Icon with Dropdown -->
        <div class="nav-item dropdown">
            <a class="nav-icon" id="profileDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= URL_ROOT ?>/profile">
                    <i class="fas fa-user-circle me-2"></i> Profile
                </a>
                <a class="dropdown-item" href="<?= URL_ROOT ?>/auth/logout">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>

        <!-- Home Icon -->
        <a class="nav-icon" href="<?= URL_ROOT ?>/">
            <i class="fa-solid fa-house"></i>
        </a>

        <!-- Placeholder Icons -->
        <a class="nav-icon"><i class="fa-solid fa-plus"></i></a>
        <a class="nav-icon"><i class="fa-solid fa-heart"></i></a>
        <a class="nav-icon"><i class="fas fa-star"></i></a>
        <a class="nav-icon"><i class="fas fa-book"></i></a>
    </div>
</nav>
