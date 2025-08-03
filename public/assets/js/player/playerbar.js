//===============================
// HIDING PLAYERBAR IN AUTH PAGES
//===============================
function isAuthPage() {
    return /\/auth\/(login|register)/i.test(window.location.pathname);
}

// Would toggle player bar visibility based on auth page status
export function togglePlayerbar() {
    if (isAuthPage()) {
        $('#playerBar, #fullPlayer').addClass('auth-hidden');
    } else {
        $('#playerBar, #fullPlayer').removeClass('auth-hidden');
    }
}

// Initial check
togglePlayerbar();

// Player state management with jQuery
const playerState = {

    currentTrack: null,
    playlist: [],
    currentIndex: -1,
    isPlaying: false,
    isRepeat: false,
    audio: new Audio(),
    progressInterval: null,

    init() {
        // Load state from sessionStorage if available
        const savedState = sessionStorage.getItem('playerState');

        // Check if we're on an auth page from the data attribute
        const isAuthPage = $('#playerBar').data('auth-page') === 'true';

        if (savedState) {
            const parsedState = JSON.parse(savedState);
            Object.assign(this, parsedState);

            // Recreate the audio object
            this.audio = new Audio();
            if (this.currentTrack) {
                this.audio.src = this.currentTrack.audio;
                if (this.isPlaying) {
                    this.audio.play().catch(e => console.error('Playback failed:', e));
                }
                this.updatePlayerUI();
                $('#playerBar').show();
            }
        }

        // Hide player elements if on auth page
        if (isAuthPage) {
            $('#playerBar').hide().css('display', 'none !important');
            $('#fullPlayer').hide().css('display', 'none !important');
            return; // Skip the rest of initialization on auth pages
        }

        // Set up event listeners
        this.setupEventListeners();
    },

    setupEventListeners() {
        // Audio events
        this.audio.addEventListener('timeupdate', this.updateProgress.bind(this));
        this.audio.addEventListener('ended', this.handleSongEnd.bind(this));
        this.audio.addEventListener('loadedmetadata', this.updateDuration.bind(this));

        // Button events
        $('#addToPlaylistBtn').on('click', this.showAddToPlaylistModal.bind(this));
        $('#playBtn, #fullPlayBtn').on('click', this.togglePlay.bind(this));
        $('#prevBtn, #fullPrevBtn').on('click', this.playPrevious.bind(this));
        $('#nextBtn, #fullNextBtn').on('click', this.playNext.bind(this));
        $('#togglePlayerBtn').on('click', this.toggleFullPlayer.bind(this));
        $('#closeFullPlayerBtn').on('click', this.toggleFullPlayer.bind(this));
        $('#repeatBtn').on('click', this.toggleRepeat.bind(this));

        // Progress bar click events
        $('#progressBar').parent().on('click', (e) => {
            const percent = e.offsetX / e.target.clientWidth;
            this.seekTo(percent);
        });

        $('#fullProgressBar').parent().on('click', (e) => {
            const percent = e.offsetX / e.target.clientWidth;
            this.seekTo(percent);
        });

        // Song card events - using event delegation for dynamically loaded content
        $(document).on('click', '.song-card', function (e) {
            if (!$(e.target).closest('a').length) { // Don't interfere with links
                const $card = $(this);
                const id = $card.data('id'); // <-- Add this line
                const audio = $card.data('audio');
                const artist = $card.data('artist');
                const title = $card.data('title');
                // Prioritize data-image, fall back to data-artwork, then default
                const image = $card.data('image') || $card.data('artwork') || 'default-image.jpg';

                // Find this song in the current playlist or create a new one
                const playlist = $('.song-card').map(function () {
                    return {
                        id: $(this).data('id'), // <-- Add this line
                        audio: $(this).data('audio'),
                        artist: $(this).data('artist'),
                        title: $(this).data('title'),
                        image: $(this).data('image') || $(this).data('artwork') || 'default-image.jpg'
                    };
                }).get();

                const index = playlist.findIndex(track => track.audio === audio);

                playerState.playTrack({
                    id, // <-- Add this line
                    audio,
                    artist,
                    title,
                    image
                }, playlist, index);

                $('#playerBar').show();
            }
        });
    },

    playTrack(track, playlist = [], index = 0) {
        this.currentTrack = track;
        this.playlist = playlist;
        this.currentIndex = index;
        this.audio.src = track.audio;

        const playAttempt = () => {
            this.audio.play()
                .then(() => {
                    this.isPlaying = true;
                    this.updatePlayerUI();
                    this.saveState();
                })
                .catch(e => {
                    console.error("Playback failed:", e);
                    // Show a tooltip near the play button (example with Bootstrap)
                    $('#playBtn').attr('title', 'Click to play').tooltip('show');
                    setTimeout(() => $('#playBtn').tooltip('hide'), 2000);
                });
        };

        // Try immediately (works during click events)
        playAttempt();
    },

    togglePlay() {
        if (this.currentTrack) {
            if (this.isPlaying) {
                this.audio.pause();
            } else {
                // Only play if user explicitly clicks the play button
                this.audio.play().catch(e => {
                    console.error('Playback failed:', e);
                    // Show a message to the user (optional)
                    alert('Please click the play button to start playback.');
                });
            }
            this.isPlaying = !this.isPlaying;
            this.updatePlayerUI();
            this.saveState();
        }
    },

    playPrevious() {
        if (this.playlist.length > 0) {
            if (this.audio.currentTime > 3) {
                // If more than 3 seconds into song, restart current song
                this.audio.currentTime = 0;
                this.updateProgress();
            } else if (this.currentIndex > 0) {
                // Go to previous song in playlist
                this.currentIndex--;
                this.playTrack(this.playlist[this.currentIndex], this.playlist, this.currentIndex);
            } else {
                // At start of playlist, restart current song
                this.audio.currentTime = 0;
                this.updateProgress();
            }
        } else {
            // No playlist, just restart current song
            this.audio.currentTime = 0;
            this.updateProgress();
        }
    },

    playNext() {
        if (this.playlist.length > 0 && this.currentIndex < this.playlist.length - 1) {
            // Go to next song in playlist
            this.currentIndex++;
            this.playTrack(this.playlist[this.currentIndex], this.playlist, this.currentIndex);
        } else {
            // End of playlist or no playlist, stop playing
            this.audio.pause();
            this.isPlaying = false;
            this.updatePlayerUI();
            this.saveState();
        }
    },

    handleSongEnd() {
        if (this.isRepeat) {
            this.audio.currentTime = 0;
            this.audio.play().catch(e => console.error('Playback failed:', e));
        } else {
            this.playNext();
        }
    },

    toggleRepeat() {
        this.isRepeat = !this.isRepeat;
        $('#repeatBtn').toggleClass('active', this.isRepeat);
        this.saveState();
    },

    seekTo(percent) {
        if (this.currentTrack) {
            this.audio.currentTime = percent * this.audio.duration;
            this.updateProgress();
        }
    },

    updateProgress() {
        if (this.currentTrack && !isNaN(this.audio.duration)) {
            const progress = (this.audio.currentTime / this.audio.duration) * 100;
            $('#progressBar, #fullProgressBar').css('width', `${progress}%`);

            // Update time displays
            $('#currentTime, #fullCurrentTime').text(this.formatTime(this.audio.currentTime));
        }
    },

    updateDuration() {
        if (!isNaN(this.audio.duration)) {
            $('#duration, #fullDuration').text(this.formatTime(this.audio.duration));
        }
    },

    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    },

    updatePlayerUI() {
        if (this.currentTrack) {
            // Update mini player
            $('#nowPlayingArt').attr('src', this.currentTrack.image);
            $('#nowPlayingTitle').text(this.currentTrack.title);
            $('#nowPlayingArtist').text(this.currentTrack.artist);

            // Update full player
            $('#fullPlayerArt').attr('src', this.currentTrack.image);
            $('#fullPlayerTitle').text(this.currentTrack.title);
            $('#fullPlayerArtist').text(this.currentTrack.artist);

            // Update play/pause buttons
            const playIcon = this.isPlaying ? 'fa-pause' : 'fa-play';
            $('#playBtn').html(`<i class="fas ${playIcon}"></i>`);
            this.audio.oncanplay = () => {
                // Update to play/pause icon when ready
                const icon = this.isPlaying ? 'fa-pause' : 'fa-play';
                $('#playBtn').html(`<i class="fas ${icon}"></i>`);
            };
            $('#fullPlayBtn').html(`<i class="fas ${playIcon} fa-2x"></i>`);
        }
    },

    toggleFullPlayer() {
        $('#fullPlayer').toggleClass('show');

        // Update toggle button icon
        const icon = $('#fullPlayer').hasClass('show') ? 'fa-chevron-down' : 'fa-chevron-up';
        $('#togglePlayerBtn').html(`<i class="fas ${icon}"></i>`);
    },

    saveState() {
        // Don't save the audio object
        const stateToSave = {
            currentTrack: this.currentTrack,
            playlist: this.playlist,
            currentIndex: this.currentIndex,
            isPlaying: this.isPlaying,
            isRepeat: this.isRepeat
        };

        sessionStorage.setItem('playerState', JSON.stringify(stateToSave));
    },

    //====================
    // PLAYLIST MANAGEMENT
    //====================
    showAddToPlaylistModal() {
        if (!this.currentTrack) return;

        const url = `${URL_ROOT}/playlists`;

        $.get(url, (playlists) => {
            // Remove any existing modal
            $('#addToPlaylistModal').remove();

            const $modal = $(`
                    <div class="modal fade" id="addToPlaylistModal" tabindex="-1" aria-labelledby="addToPlaylistLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-dark text-white">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addToPlaylistLabel">Add to Playlist</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group">
                                        ${playlists.map(p => `
                                            <li class="list-group-item d-flex justify-content-between bg-dark text-white" data-id="${p.id}">
                                                ${p.name}
                                                <i class="fas ${p.songs.includes(playerState.currentTrack.id) ? 'fa-check-circle text-success' : 'fa-circle'}"></i>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary cancel-btn" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary add-btn">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

            $('body').append($modal);

            // Bootstrap modal instance
            const modalInstance = new bootstrap.Modal(document.getElementById('addToPlaylistModal'));
            modalInstance.show();

            $modal.find('.list-group-item').on('click', function () {
                $(this).find('i').toggleClass('fa-circle fa-check-circle text-success');
            });

            $modal.find('.add-btn').on('click', () => {
                const selected = $modal.find('.fa-check-circle').closest('li');
                const playlistIds = selected.map(function () {
                    return $(this).data('id');
                }).get();

                playerState.addToPlaylists(playlistIds);
                modalInstance.hide();
            });

            // Remove modal from DOM after hidden
            $modal.on('hidden.bs.modal', function () {
                $modal.remove();
            });
        });
    },

    addToPlaylists: function (playlistIds) {
        if (!this.currentTrack || playlistIds.length === 0) return;
        const url = `${URL_ROOT}/playlist/add-song`;

        playlistIds.forEach(playlistId => {
            console.log('Adding song:', {
                playlistId: playlistId,
                songId: this.currentTrack.id
            }); // <-- Add this line

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    playlistId: playlistId,
                    songId: this.currentTrack.id
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Track added to selected playlists!');
                    } else {
                        alert('Failed to add track to playlists: ' + data.message);
                    }
                })
                .catch(() => {
                    alert('Failed to add track to playlists.');
                });
        });
    },
};

// Initialize player
window.playerState = playerState;

// Export the playerState for module usage
export { playerState };

// to prevent any accidental showing on auth pages:
$(document).ready(function () {
    // Double-check auth status on page load
    const isAuthPage = $('#playerBar').data('auth-page') === 'true';
    if (isAuthPage) {
        $('#playerBar, #fullPlayer').hide().css({
            'display': 'none !important',
            'visibility': 'hidden !important'
        });
    }

    // Optional: Prevent any show() calls on auth pages
    const originalShow = $.fn.show;
    $.fn.show = function () {
        if ($(this).is('#playerBar, #fullPlayer') &&
            $(this).data('auth-page') === 'true') {
            return this; // Don't show on auth pages
        }
        return originalShow.apply(this, arguments);
    };
});

// Make sure player persists during SPA navigation
$(document).on('spa:navigate', () => {
    playerState.saveState();
});

$(document).on('spa:loaded', () => {
    playerState.init();
});
