 
                <!-- Search form -->
                <div class="row mt-3 col-8 justify-content-between">
                        <div class="input-group mb-3">
                            <input type="text" 
                            id="searchInput" 
                            class="form-control" 
                            placeholder="Search for songs, artists..." 
                            aria-label="Search"
                            value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" style="width: 500px">
                        </div>
                </div>
