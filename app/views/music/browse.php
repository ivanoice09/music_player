<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <h1 class="my-4">Browse Music</h1>

    <?php if (!empty($data['tracks'])): ?>
        <div class="row">
            <?php foreach ($data['tracks'] as $track): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo $track['image']; ?>" class="card-img-top" alt="<?php echo $track['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $track['name']; ?></h5>
                            <p class="card-text">
                                Artist: <?php echo $track['artist_name']; ?><br>
                                Duration: <?php echo gmdate("i:s", $track['duration']); ?>
                            </p>
                            <audio controls class="w-100">
                                <source src="<?php echo $track['audio']; ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No tracks found</div>
    <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>