<nav class="navbar navbar-nav sticky-top">
    <div class="container-fluid">
        <div class="d-flex align-items-center w-100">

            <!-- FLOATING ACTION BUTTON  -->
            <div class="fab-container me-2 me-md-3">
                <div class="fab" id="mainFab">
                    <div class="fab-content" id="fabContent">
                        <div class="fab-menu-icon-container">
                            <i class="fas fa-plus" id="fabIcon"></i>
                        </div>
                        <div class="fab-icons" id="fabIcons">
                            <div class="fab-icon" title="Home">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="fab-icon" title="Search">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="fab-icon" title="Settings">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="fab-icon" title="User Profile">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FLOATING SEARCH BAR -->
            <div class="floating-search-container d-flex">
                <div class="search-bar" id="searchBar">
                    <div class="floating-search position-relative mx-auto">
                        <input type="text"
                            id="searchInput"
                            class="form-control"
                            placeholder="Search for songs, artists..."
                            aria-label="Search"
                            value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                        <i class="fas fa-search search-icon position-absolute top-50 translate-middle-y" style="left: 15px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fab = document.getElementById('mainFab');
        const fabIcon = document.getElementById('fabIcon');
        const fabContent = document.getElementById('fabContent');
        const fabIcons = document.getElementById('fabIcons');
        const searchBar = document.getElementById('searchBar');
        const searchInput = document.getElementById('searchInput');

        let expanded = false;
        let isMobile = window.matchMedia("(max-width: 768px)").matches;

        // Update screen size indicator
        function updateScreenSize() {
            isMobile = window.matchMedia("(max-width: 768px)").matches;

            // If expanded and screen size changed, close the menu
            if (expanded) {
                toggleFab();
            }
        }

        window.addEventListener('resize', updateScreenSize);
        updateScreenSize();

        // Toggle FAB expansion
        function toggleFab() {
            expanded = !expanded;

            if (expanded) {
                fab.classList.add('fab-expanded');
                fabIcon.classList.remove('fa-bars');
                fabIcon.classList.add('fa-times');

                // Only shrink search bar on mobile
                if (isMobile) {
                    searchBar.classList.add('search-bar-shrinked');
                    searchInput.blur(); // Remove focus from search input
                }
            } else {
                fab.classList.remove('fab-expanded');
                fabIcon.classList.remove('fa-times');
                fabIcon.classList.add('fa-bars');

                // Restore search bar if it was shrunk
                if (isMobile) {
                    searchBar.classList.remove('search-bar-shrinked');
                }
            }
        }

        fab.addEventListener('click', function() {
            toggleFab();
        });

        // Add click events to the icon buttons
        const fabIconElements = document.querySelectorAll('.fab-icon');
        fabIconElements.forEach(icon => {
            icon.addEventListener('click', function(e) {
                e.stopPropagation();
                // Close the menu when an icon is clicked on mobile
                if (isMobile && expanded) {
                    toggleFab();
                }
            });
        });

        // Close menu when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (isMobile && expanded && !fab.contains(e.target)) {
                toggleFab();
            }
        });
    });
</script>