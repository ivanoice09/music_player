<div class="row mt-3">
    <div class="col-md-8 mx-auto">
        <div class="input-group mb-3">
            <input type="text" id="searchInput" class="form-control" 
                   placeholder="Search for songs, artists..." 
                   aria-label="Search"
                   value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" style="width: 500px">
        </div>
    </div>
</div>

<script>
// Make URL_ROOT available to JavaScript
const URL_ROOT = '<?= URL_ROOT ?>';
</script>