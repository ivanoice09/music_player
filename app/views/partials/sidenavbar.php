<!-- NAVBAR 2.0 -->
<nav class="navbar-vertical fixed-left">
    <div class="navbar-icons">
        <!-- Profile Icon with Popover -->
        <div class="nav-item">
            <a class="nav-icon" 
                id="profilePopover" 
                tabindex="0" 
                data-bs-toggle="popover" 
                data-bs-placement="right" 
                data-bs-trigger="focus">

                <i class="bi bi-person-fill"></i>
            </a>
        </div>

        <!-- Home Icon -->
        <a class="nav-icon" href="<?php echo URL_ROOT; ?>/" id="homeLink">
            <i class="bi bi-house-door-fill"></i>
        </a>

        <!-- Placeholder Icons -->
        <a class="nav-icon" href="#" id="createPlaylistBtn"><i class="bi bi-plus-circle-fill"></i></a>
        <a class="nav-icon"><i class="bi bi-heart-fill"></i></a>
        <a class="nav-icon"><i class="bi bi-star-fill"></i></a>
        <a class="nav-icon" href="#" id="library-link"><i class="bi bi-collection-fill"></i></a>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isLoggedIn = <?= isLoggedIn() ? 'true' : 'false' ?>;
        const profilePopover = document.getElementById('profilePopover');

        // Set popover content based on login status
        const popoverContent = isLoggedIn ?
            `
            <div class="popover-profile-menu">
                <a class="dropdown-item" href="<?= URL_ROOT ?>/profile">
                    <i class="bi bi-person-square"></i> Profile
                </a>
                <a class="dropdown-item" href="<?= URL_ROOT ?>/auth/logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
          ` :
            `
            <div class="popover-profile-menu">
                <a class="dropdown-item" href="<?= URL_ROOT ?>/auth/login">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </a>
                <a class="dropdown-item" href="<?= URL_ROOT ?>/auth/register">
                    <i class="bi bi-person-plus"></i> Register
                </a>
            </div>
          `;

        // Initialize popover
        new bootstrap.Popover(profilePopover, {
            html: true,
            content: popoverContent,
            placement: 'right',
            trigger: 'focus'
        });
    });
</script>