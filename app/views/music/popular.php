<div id="popular-container" class="row">
    <?php foreach ($songs ?? [] as $song): ?>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <img src="<?= $song['image'] ?>" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title"><?= $song['name'] ?></h5>
                    <p class="card-text"><?= $song['artist_name'] ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>