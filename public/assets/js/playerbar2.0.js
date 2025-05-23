// Player state management
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
        document.getElementById('playBtn').addEventListener('click', this.togglePlay.bind(this));
        document.getElementById('fullPlayBtn').addEventListener('click', this.togglePlay.bind(this));
        document.getElementById('prevBtn').addEventListener('click', this.playPrevious.bind(this));
        document.getElementById('fullPrevBtn').addEventListener('click', this.playPrevious.bind(this));
        document.getElementById('nextBtn').addEventListener('click', this.playNext.bind(this));
        document.getElementById('fullNextBtn').addEventListener('click', this.playNext.bind(this));
        document.getElementById('togglePlayerBtn').addEventListener('click', this.toggleFullPlayer.bind(this));
        document.getElementById('closeFullPlayerBtn').addEventListener('click', this.toggleFullPlayer.bind(this));
        document.getElementById('repeatBtn').addEventListener('click', this.toggleRepeat.bind(this));

        // Progress bar click events
        document.getElementById('progressBar').parentElement.addEventListener('click', (e) => {
            const percent = e.offsetX / e.target.clientWidth;
            this.seekTo(percent);
        });

        document.getElementById('fullProgressBar').parentElement.addEventListener('click', (e) => {
            const percent = e.offsetX / e.target.clientWidth;
            this.seekTo(percent);
        });

        // Song card events
        document.querySelectorAll('.song-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('a')) { // Don't interfere with links
                    const audio = card.dataset.audio;
                    const artist = card.dataset.artist;
                    const title = card.dataset.title;
                    const image = card.dataset.image;

                    // Find this song in the current playlist or create a new one
                    const playlist = Array.from(document.querySelectorAll('.song-card')).map(card => ({
                        audio: card.dataset.audio,
                        artist: card.dataset.artist,
                        title: card.dataset.title,
                        image: card.dataset.image
                    }));

                    const index = playlist.findIndex(track => track.audio === audio);

                    this.playTrack({
                        audio,
                        artist,
                        title,
                        image
                    }, playlist, index);
                }
            });
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
        document.getElementById('repeatBtn').classList.toggle('active', this.isRepeat);
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
            document.getElementById('progressBar').style.width = `${progress}%`;
            document.getElementById('fullProgressBar').style.width = `${progress}%`;

            // Update time displays
            document.getElementById('currentTime').textContent = this.formatTime(this.audio.currentTime);
            document.getElementById('fullCurrentTime').textContent = this.formatTime(this.audio.currentTime);
        }
    },

    updateDuration() {
        if (!isNaN(this.audio.duration)) {
            document.getElementById('duration').textContent = this.formatTime(this.audio.duration);
            document.getElementById('fullDuration').textContent = this.formatTime(this.audio.duration);
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
            document.getElementById('nowPlayingArt').src = this.currentTrack.image;
            document.getElementById('nowPlayingTitle').textContent = this.currentTrack.title;
            document.getElementById('nowPlayingArtist').textContent = this.currentTrack.artist;

            // Update full player
            document.getElementById('fullPlayerArt').src = this.currentTrack.image;
            document.getElementById('fullPlayerTitle').textContent = this.currentTrack.title;
            document.getElementById('fullPlayerArtist').textContent = this.currentTrack.artist;

            // Update play/pause buttons
            const playIcon = this.isPlaying ? 'fa-pause' : 'fa-play';
            document.getElementById('playBtn').innerHTML = `<i class="fas ${playIcon}"></i>`;
            document.getElementById('fullPlayBtn').innerHTML = `<i class="fas ${playIcon} fa-2x"></i>`;
        }
    },

    toggleFullPlayer() {
        const fullPlayer = document.getElementById('fullPlayer');
        fullPlayer.classList.toggle('show');

        // Update toggle button icon
        const icon = fullPlayer.classList.contains('show') ? 'fa-chevron-down' : 'fa-chevron-up';
        document.getElementById('togglePlayerBtn').innerHTML = `<i class="fas ${icon}"></i>`;
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

// Initialize player when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    playerState.init();
});

// Save state before page unload
window.addEventListener('beforeunload', () => {
    playerState.saveState();
});