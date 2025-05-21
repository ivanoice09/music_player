// Global Player State
const globalPlayer = {
    audio: document.getElementById('audioElement'),
    currentTrack: null,
    queue: [],
    currentIndex: -1,

    init: function () {
        // Player elements
        this.elements = {
            player: document.getElementById('globalPlayer'),
            artwork: document.getElementById('playerArtwork'),
            title: document.getElementById('playerTrackTitle'),
            artist: document.getElementById('playerTrackArtist'),
            playPauseBtn: document.getElementById('playPauseBtn'),
            progressBar: document.getElementById('progressBar'),
            currentTime: document.getElementById('currentTime'),
            duration: document.getElementById('duration'),
            volumeControl: document.getElementById('volumeControl')
        };

        // Event listeners
        this.audio.addEventListener('timeupdate', this.updateProgress.bind(this));
        this.audio.addEventListener('ended', this.nextTrack.bind(this));
        this.elements.playPauseBtn.addEventListener('click', this.togglePlay.bind(this));
        this.elements.volumeControl.addEventListener('input', this.setVolume.bind(this));

        // Initialize volume
        this.audio.volume = this.elements.volumeControl.value;
    },

    loadTrack: function (track) {
        this.currentTrack = track;
        this.audio.src = track.audio;

        // Update UI
        this.elements.artwork.src = track.image;
        this.elements.artwork.style.display = 'block';
        this.elements.title.textContent = track.name;
        this.elements.artist.textContent = track.artist_name;

        // Show player if hidden
        this.elements.player.style.display = 'flex';

        // Play the track
        this.audio.play().then(() => {
            this.elements.playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        }).catch(error => {
            console.log("Playback failed:", error);
        });

        // Set duration when metadata loads
        this.audio.addEventListener('loadedmetadata', () => {
            this.elements.duration.textContent = this.formatTime(this.audio.duration);
        });
    },

    togglePlay: function () {
        if (this.audio.paused) {
            this.audio.play();
            this.elements.playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        } else {
            this.audio.pause();
            this.elements.playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        }
    },

    updateProgress: function () {
        const progress = (this.audio.currentTime / this.audio.duration) * 100;
        this.elements.progressBar.style.width = progress + '%';
        this.elements.currentTime.textContent = this.formatTime(this.audio.currentTime);
    },

    setVolume: function () {
        this.audio.volume = this.elements.volumeControl.value;
    },

    formatTime: function (seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    },

    nextTrack: function () {
        // Implement if you want playlist functionality
    },

    prevTrack: function () {
        // Implement if you want playlist functionality
    }
};

// Initialize player when DOM loads
document.addEventListener('DOMContentLoaded', function () {
    globalPlayer.init();

    // Set up song card clicks
    document.addEventListener('click', function (e) {
        // Handle play button clicks
        if (e.target.closest('.play-button') || e.target.closest('.song-card')) {
            const card = e.target.closest('.song-card');
            const track = {
                audio: card.dataset.audio,
                name: card.dataset.title,
                artist_name: card.dataset.artist,
                image: card.dataset.image
            };

            globalPlayer.loadTrack(track);
        }
    });

});