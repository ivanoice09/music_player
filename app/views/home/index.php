<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <p class="h3 my-5"><strong>Featured tracks</strong></p>
    <div class="row">
        <?php if (!empty($data['featuredTracks']['results'])): ?>
            <?php foreach ($data['featuredTracks']['results'] as $track): ?>
                <div class="col-md-3 mb-5 song-card"
                    data-audio="<?php echo htmlspecialchars($track['audio'] ?? $track['audiodownload'] ?? ''); ?>"
                    data-artist="<?php echo htmlspecialchars($track['artist_name'] ?? 'Unknown Artist'); ?>"
                    data-title="<?php echo htmlspecialchars($track['name'] ?? 'Unknown Track'); ?>"
                    data-image="<?php echo htmlspecialchars($track['image'] ?? ''); ?>">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($track['image'] ?? 'placeholder.jpg'); ?>" class="card-img-top" alt="Album Art">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($track['name'] ?? 'Unknown Track'); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($track['artist_name'] ?? 'Unknown Artist'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tracks found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>