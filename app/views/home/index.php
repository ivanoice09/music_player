<!-- HOMEPAGE -->
<div class="container">
    <p class="h3 my-5"><strong>You're at the homepage</strong></p>
    <div class="row">
        <?php foreach ($featuredTracks['results'] as $track): ?>
            <div class="col-md-3 mb-4">
                <div class="song-card card h-100" 
                     data-audio="<?= $track['audio'] ?>" 
                     data-title="<?= htmlspecialchars($track['name']) ?>" 
                     data-artist="<?= htmlspecialchars($track['artist_name']) ?>" 
                     data-image="<?= $track['image'] ?>">
                    <img src="<?= $track['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($track['name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($track['name']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars($track['artist_name']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>