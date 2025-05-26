// Player state management with jQuery
$(document).ready(function () {
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

            // Set up event listeners
            this.setupEventListeners();
        },

        setupEventListeners() {
            // Audio events
            this.audio.addEventListener('timeupdate', this.updateProgress.bind(this));
            this.audio.addEventListener('ended', this.handleSongEnd.bind(this));
            this.audio.addEventListener('loadedmetadata', this.updateDuration.bind(this));

            // Button events
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
                    const audio = $card.data('audio');
                    const artist = $card.data('artist');
                    const title = $card.data('title');
                    const image = $card.data('image') || $card.data('artwork'); // Handle both data-image and data-artwork

                    // Find this song in the current playlist or create a new one
                    const playlist = $('.song-card').map(function () {
                        return {
                            audio: $(this).data('audio'),
                            artist: $(this).data('artist'),
                            title: $(this).data('title'),
                            image: $(this).data('image')
                        };
                    }).get();

                    const index = playlist.findIndex(track => track.audio === audio);

                    playerState.playTrack({
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
            this.audio.play().then(() => {
                this.isPlaying = true;
                this.updatePlayerUI();
                this.saveState();
            }).catch(e => {
                console.error('Playback failed:', e);
            });
        },

        togglePlay() {
            if (this.currentTrack) {
                if (this.isPlaying) {
                    this.audio.pause();
                } else {
                    this.audio.play().catch(e => console.error('Playback failed:', e));
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
        }
    };

    // Initialize player
    window.playerState = playerState;
    playerState.init();

     // Make sure player persists during SPA navigation
    $(document).on('spa:navigate', () => {
        playerState.saveState();
    });

    $(document).on('spa:loaded', () => {
        playerState.init();
    });
});