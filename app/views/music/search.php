
<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container">
    <h1 class="my-4">Music Search</h1>
    
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="searchForm" action="<?php echo URL_ROOT; ?>/music/search" method="post">
                <div class="form-row">
                    <div class="col-md-8 mb-3">
                        <div class="input-group">
                            <input type="text" name="query" id="searchInput" class="form-control" 
                                   placeholder="Search for songs, artists..." 
                                   value="<?php echo htmlspecialchars($data['query']); ?>"
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <div id="searchSuggestions" class="list-group suggestions-dropdown"></div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="genre">Genre</label>
                        <select name="genre" id="genre" class="form-control">
                            <option value="">All Genres</option>
                            <option value="rock" <?php echo $data['filters']['genre'] === 'rock' ? 'selected' : ''; ?>>Rock</option>
                            <option value="pop" <?php echo $data['filters']['genre'] === 'pop' ? 'selected' : ''; ?>>Pop</option>
                            <option value="electronic" <?php echo $data['filters']['genre'] === 'electronic' ? 'selected' : ''; ?>>Electronic</option>
                            <option value="jazz" <?php echo $data['filters']['genre'] === 'jazz' ? 'selected' : ''; ?>>Jazz</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="duration_min">Min Duration (sec)</label>
                        <input type="number" name="duration_min" id="duration_min" 
                               class="form-control" min="0" max="600"
                               value="<?php echo $data['filters']['duration_min']; ?>">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="duration_max">Max Duration (sec)</label>
                        <input type="number" name="duration_max" id="duration_max" 
                               class="form-control" min="0" max="600"
                               value="<?php echo $data['filters']['duration_max']; ?>">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="order">Sort By</label>
                        <select name="order" id="order" class="form-control">
                            <option value="popularity_total" <?php echo $data['filters']['order'] === 'popularity_total' ? 'selected' : ''; ?>>Popularity</option>
                            <option value="releasedate" <?php echo $data['filters']['order'] === 'releasedate' ? 'selected' : ''; ?>>Release Date</option>
                        </select>
                    </div>
                </div>
                
                <input type="hidden" name="page" id="pageInput" value="1">
            </form>
        </div>
    </div>

    <!-- Search Results -->
    <?php if ($data['search_performed']): ?>
        <?php if (!empty($data['results'])): ?>
            <div class="alert alert-info">
                Found <?php echo $data['total_results']; ?> results for "<?php echo htmlspecialchars($data['query']); ?>"
            </div>
            
            <div class="row">
                <?php foreach ($data['results'] as $track): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo $track['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($track['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($track['name']); ?></h5>
                                <p class="card-text">
                                    <strong>Artist:</strong> <?php echo htmlspecialchars($track['artist_name']); ?><br>
                                    <strong>Duration:</strong> <?php echo gmdate("i:s", $track['duration']); ?><br>
                                    <?php if (!empty($track['tags'])): ?>
                                        <strong>Tags:</strong> <?php echo implode(', ', array_map('htmlspecialchars', explode(' ', $track['tags']))); ?>
                                    <?php endif; ?>
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
            
            <!-- Pagination -->
            <?php if ($data['total_pages'] > 1): ?>
                <nav aria-label="Search results pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $data['current_page'] <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="#" data-page="<?php echo $data['current_page'] - 1; ?>">Previous</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                            <li class="page-item <?php echo $i == $data['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo $data['current_page'] >= $data['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="#" data-page="<?php echo $data['current_page'] + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">
                No results found for "<?php echo htmlspecialchars($data['query']); ?>"
            </div>
        <?php endif; ?>
    <?php elseif (!empty($data['suggestions'])): ?>
        <!-- Search Suggestions -->
        <div class="card">
            <div class="card-header">
                <h5>Popular Tracks</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php foreach ($data['suggestions'] as $suggestion): ?>
                        <a href="<?php echo URL_ROOT; ?>/music/search?query=<?php echo urlencode($suggestion['name']); ?>" class="list-group-item list-group-item-action">
                            <strong><?php echo htmlspecialchars($suggestion['name']); ?></strong> by <?php echo htmlspecialchars($suggestion['artist']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>