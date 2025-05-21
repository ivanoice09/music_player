document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('headerSearchInput');
    const resultsContainer = document.getElementById('instantResults');
    const playerBar = document.getElementById('playerBar');
    const audioPlayer = document.getElementById('audioPlayer');
    const nowPlayingTitle = document.getElementById('nowPlayingTitle');
    const nowPlayingArtist = document.getElementById('nowPlayingArtist');
    const nowPlayingArtwork = document.getElementById('nowPlayingArtwork');
    let searchTimeout;

    // Handle song card clicks
    function setupSongCardEvents() {
        document.querySelectorAll('.song-card').forEach(card => {
            card.addEventListener('click', function () {
                const audioSrc = this.dataset.audio;
                const title = this.dataset.title;
                const artist = this.dataset.artist;
                const artwork = this.dataset.image;

                // Update player bar
                nowPlayingTitle.textContent = title;
                nowPlayingArtist.textContent = artist;
                nowPlayingArtwork.src = artwork;
                nowPlayingArtwork.style.display = 'block';

                // Update audio source
                audioPlayer.src = audioSrc;
                audioPlayer.load();
                audioPlayer.play().catch(e => console.log("Auto-play prevented:", e));

                // Show player bar
                playerBar.style.display = 'block';
            });
        });
    }

    // Search function
    function fetchInstantResults(query) {
        fetch(`${baseUrl}/music/instantSearch?q=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                resultsContainer.innerHTML = '';

                if (data.results && data.results.length > 0) {
                    data.results.forEach(track => {
                        const col = document.createElement('div');
                        col.className = 'col-md-4 mb-4';
                        // In the card generation code:
                        col.innerHTML = `
                        <div class="card song-card h-100" 
                            data-audio="${track.audio}"
                            data-title="${track.name}"
                            data-artist="${track.artist_name}"
                            data-image="${track.image}">
                            <img src="${track.image}" class="card-img-top" alt="${track.name}">
                            <div class="card-body">
                                <h5 class="card-title">${track.name}</h5>
                                <p class="card-text">
                                    <strong>Artist:</strong> ${track.artist_name}<br>
                                    <strong>Duration:</strong> ${formatDuration(track.duration)}
                                </p>
                                <button class="btn btn-sm btn-primary play-button">
                                    <i class="fas fa-play"></i> Play
                                </button>
                            </div>
                        </div>

                    `;
                        resultsContainer.appendChild(col);
                    });

                    // Set up event listeners for new cards
                    setupSongCardEvents();
                } else {
                    resultsContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info">No results found for "${query}"</div>
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultsContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">Failed to load search results</div>
                </div>
            `;
            });
    }

    // Format duration (MM:SS)
    function formatDuration(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }

    // Search input handler
    if (searchInput && resultsContainer) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length > 1) {
                searchTimeout = setTimeout(() => {
                    fetchInstantResults(query);
                }, 300);
            } else {
                resultsContainer.innerHTML = '';
            }
        });
    }
});

