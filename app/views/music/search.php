<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <h1 class="my-4">Search Results</h1>
    <!-- Search Results -->
    <?php if ($data['search_performed']): ?>
        <?php if (!empty($data['results'])): ?>
            <div class="row">
                <?php foreach ($data['results'] as $track): ?>
                    <div class="col-md-4 mb-4 song-card"
                        data-audio="<?php echo htmlspecialchars($track['audio'] ?? $track['audiodownload'] ?? ''); ?>"
                        data-artist="<?php echo htmlspecialchars($track['artist_name'] ?? 'Unknown Artist'); ?>"
                        data-title="<?php echo htmlspecialchars($track['name'] ?? 'Unknown Track'); ?>"
                        data-image="<?php echo htmlspecialchars($track['image'] ?? ''); ?>">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($track['image'] ?? 'placeholder.jpg'); ?>" class="card-img-top" alt="Album Art">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($track['name'] ?? 'Unknown Track'); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($track['artist_name'] ?? 'Unknown Artist'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                No results found for "<?php echo htmlspecialchars($data['query']); ?>"
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-secondary">
            Enter a search term to find music
        </div>
    <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>