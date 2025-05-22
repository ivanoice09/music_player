document.addEventListener('DOMContentLoaded', function () {
    // Initialize player bar
    const playerBar = document.getElementById('playerBar');
    const audioPlayer = document.getElementById('audioPlayer');
    const nowPlayingArt = document.getElementById('nowPlayingArt');
    const nowPlayingTitle = document.getElementById('nowPlayingTitle');
    const nowPlayingArtist = document.getElementById('nowPlayingArtist');

    // Hide player bar initially
    playerBar.style.display = 'none';

    // Add click event to all song cards
    document.querySelectorAll('.song-card').forEach(card => {
        card.addEventListener('click', function () {
            // Get track data from data attributes
            const audioUrl = this.dataset.audio;
            const title = this.dataset.title;
            const artist = this.dataset.artist;
            const image = this.dataset.image;

            // Update player bar
            nowPlayingArt.src = image || 'placeholder.jpg';
            nowPlayingTitle.textContent = title;
            nowPlayingArtist.textContent = artist;
            audioPlayer.src = audioUrl;

            // Show player bar
            playerBar.style.display = 'block';

            // Play the audio
            audioPlayer.play();
        });
    });

    // Optional: Add event listener for when audio ends
    audioPlayer.addEventListener('ended', function () {
        // You could add logic to play the next song here
    });
});