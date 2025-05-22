document.addEventListener('DOMContentLoaded', function () {
    // Sample data - replace with your Jamendo API response
    const songs = [
        {
            id: 1,
            title: "Sample Song 1",
            artist: "Artist 1",
            albumArt: "https://via.placeholder.com/150",
            audioUrl: "https://your-jamendo-api-stream-url/song1.mp3"
        },
        {
            id: 2,
            title: "Sample Song 2",
            artist: "Artist 2",
            albumArt: "https://via.placeholder.com/150",
            audioUrl: "https://your-jamendo-api-stream-url/song2.mp3"
        },
        // Add more songs as needed
    ];

    const songGrid = document.getElementById('songGrid');
    const playerBar = document.getElementById('playerBar');
    const audioPlayer = document.getElementById('audioPlayer');
    const nowPlayingTitle = document.getElementById('nowPlayingTitle');
    const nowPlayingArtist = document.getElementById('nowPlayingArtist');
    const nowPlayingArt = document.getElementById('nowPlayingArt');

    // Function to render song cards
    function renderSongCards() {
        songGrid.innerHTML = '';

        songs.forEach(song => {
            const card = document.createElement('div');
            card.className = 'col-md-4 mb-4';
            card.innerHTML = `
                <div class="card song-card" data-id="${song.id}">
                    <img src="${song.albumArt}" class="card-img-top album-art" alt="${song.title}">
                    <div class="card-body">
                        <h5 class="card-title">${song.title}</h5>
                        <p class="card-text text-muted">${song.artist}</p>
                    </div>
                </div>
            `;

            card.addEventListener('click', () => playSong(song));
            songGrid.appendChild(card);
        });
    }

    // Function to play a song
    function playSong(song) {
        nowPlayingTitle.textContent = song.title;
        nowPlayingArtist.textContent = song.artist;
        nowPlayingArt.src = song.albumArt;

        audioPlayer.src = song.audioUrl;
        audioPlayer.play();

        playerBar.style.display = 'block';
    }

    // Initialize the app
    renderSongCards();

    // If you're fetching from Jamendo API, you might have something like:
    /*
    fetch('your-jamendo-api-endpoint')
        .then(response => response.json())
        .then(data => {
            songs = data.results; // Adjust according to your API response structure
            renderSongCards();
        })
        .catch(error => console.error('Error fetching songs:', error));
    */
});