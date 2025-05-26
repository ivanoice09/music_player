<!-- SENDING DATA THROUGH FORM -->
<!-- <form action="<?php echo URL_ROOT; ?>/music/search" method="post" style="width: 400px;">
    <div class="input-group">
        <input type="text" name="query" class="form-control"
            placeholder="Search for songs..."
            value="<?php echo !empty($data['query']) ? $data['query'] : ''; ?>">
    </div>
</form> -->

<!-- Search Bar with ID: SENDING DATA THROUGH AJAX -->
<div class="row mt-3">
    <div class="col-md-8 mx-auto">
        <div class="input-group mb-3">
            <input type="text" id="searchInput" class="form-control" 
                   placeholder="Search for songs, artists..." 
                   aria-label="Search"
                   value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
            <button class="btn btn-primary" type="button" id="searchButton">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</div>

<script>
// Make URL_ROOT available to JavaScript
const URL_ROOT = '<?= URL_ROOT ?>';
</script>