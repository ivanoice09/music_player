<!-- FLOATING SEARCH BAR -->
<div class="floating-search-container">
    <div class="floating-search">
        <input type="text" 
               id="searchInput" 
               class="form-control" 
               placeholder="Search for songs, artists..." 
               aria-label="Search"
               value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <i class="fas fa-search search-icon"></i>
    </div>
</div>
