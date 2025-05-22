<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Player</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .player-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #f8f9fa;
            padding: 15px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }

        .song-card {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .song-card:hover {
            transform: scale(1.03);
        }

        .album-art {
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">Music Library</h1>

        <!-- Song Cards Grid -->
        <div class="row" id="songGrid">
            <!-- Song cards will be dynamically inserted here -->
        </div>
    </div>

    <!-- Player Bar -->
    <div class="player-bar" id="playerBar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="" alt="Album Art" id="nowPlayingArt" class="img-thumbnail" style="width: 60px; height: 60px;">
                </div>
                <div class="col-md-6">
                    <h6 id="nowPlayingTitle">No song selected</h6>
                    <p class="text-muted mb-0" id="nowPlayingArtist">Artist</p>
                </div>
                <div class="col-md-4">
                    <audio id="audioPlayer" controls></audio>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="playerbar_test.js"></script>
</body>

</html>