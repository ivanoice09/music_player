<form action="<?php echo URL_ROOT; ?>/music/search" method="post" style="width: 400px;">
    <div class="input-group">
        <input type="text" name="query" class="form-control"
            placeholder="Search for songs..."
            value="<?php echo !empty($data['query']) ? $data['query'] : ''; ?>">
    </div>
</form>
