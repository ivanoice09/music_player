<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <h1 class="my-4">Search Results</h1>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <form action="<?php echo URL_ROOT; ?>/music/search" method="post">
                <div class="input-group">
                    <input type="text" name="query" class="form-control"
                        placeholder="Search for music..."
                        value="<?php echo !empty($data['query']) ? $data['query'] : ''; ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    <?php if ($data['search_performed']): ?>
        <?php if (!empty($data['results'])): ?>
            <div class="row">
                <?php foreach ($data['results'] as $track): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo $track['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($track['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($track['name']); ?></h5>
                                <p class="card-text">
                                    Artist: <?php echo htmlspecialchars($track['artist_name']); ?><br>
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