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
            <div class="d-flex">
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
</nav>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fab = document.getElementById('mainFab');
        const fabIcon = document.getElementById('fabIcon');
        const fabContent = document.getElementById('fabContent');
        const fabIcons = document.getElementById('fabIcons');
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
                if (isMobile) {
                    fab.classList.add('fab-expanded-mobile');
                    fabContent.classList.add('fab-content-vertical');
                    fabIcons.classList.add('fab-icons-vertical');
                } else {
                    fab.classList.add('fab-expanded');
                }
                fab.classList.remove('pulse');
                fabIcon.classList.remove('fa-bars');
                fabIcon.classList.add('fa-times');
            } else {
                fab.classList.remove('fab-expanded', 'fab-expanded-mobile');
                fabContent.classList.remove('fab-content-vertical');
                fabIcons.classList.remove('fab-icons-vertical');
                fab.classList.add('pulse');
                fabIcon.classList.remove('fa-times');
                fabIcon.classList.add('fa-bars');
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
            });
        });
    });
</script>