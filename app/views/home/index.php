<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <?php flash('register_success'); ?>
    
    <h1 class="my-4">Welcome to <?php echo SITE_NAME; ?></h1>
    
    <?php if(!empty($data['featuredTracks'])): ?>
        <h3>Featured Tracks</h3>
        <div class="row">
            <?php foreach($data['featuredTracks'] as $track): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo $track['image']; ?>" class="card-img-top" alt="<?php echo $track['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $track['name']; ?></h5>
                            <p class="card-text">Artist: <?php echo $track['artist_name']; ?></p>
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
        <div class="alert alert-warning">No featured tracks found</div>
    <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>