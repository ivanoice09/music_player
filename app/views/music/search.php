<div id="search-container">
    <ul class="list-group">
        <?php foreach ($songs ?? [] as $song): ?>
            <li class="list-group-item">
                <div class="d-flex">
                    <img src="<?= $song['image'] ?>" width="60" height="60" class="me-3">
                    <div>
                        <h5><?= $song['name'] ?></h5>
                        <p class="mb-0"><?= $song['artist_name'] ?></p>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>