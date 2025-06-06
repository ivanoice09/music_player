<!-- PLAYERBAR (HIDDEN BY DEFAULT) -->
<div class="player-bar" id="playerBar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-2">
                <img src="" alt="Album Art" id="nowPlayingArt" class="img-thumbnail" style="width: 60px; height: 60px;">
            </div>
            <div class="col-md-6">
                <h6 id="nowPlayingTitle">No song selected</h6>
                <p class="text-muted mb-0" id="nowPlayingArtist">Artist</p>
                <div class="progress mt-2" style="height: 3px;">
                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <button id="prevBtn" class="btn btn-link">
                        <i class="fas fa-step-backward"></i>
                    </button>
                    <button id="playBtn" class="btn btn-link">
                        <i class="fas fa-play"></i>
                    </button>
                    <button id="nextBtn" class="btn btn-link">
                        <i class="fas fa-step-forward"></i>
                    </button>
                    <span id="currentTime">0:00</span>
                    <span id="duration">0:00</span>
                    <button id="togglePlayerBtn" class="btn btn-link">
                        <i class="fas fa-chevron-up"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Player View (hidden by default) -->
<div class="full-player" id="fullPlayer">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12 text-center">
                <button id="closeFullPlayerBtn" class="btn btn-link position-absolute top-0 end-0 m-3">
                    <i class="fas fa-times fa-2x"></i>
                </button>
                <img id="fullPlayerArt" src="" alt="Album Art" class="img-fluid mb-4" style="max-height: 300px;">
                <h2 id="fullPlayerTitle">No song selected</h2>
                <p class="text-muted" id="fullPlayerArtist">Artist</p>
                <div class="progress mb-3" style="height: 5px;">
                    <div id="fullProgressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <button id="fullPrevBtn" class="btn btn-link mx-3">
                        <i class="fas fa-step-backward fa-2x"></i>
                    </button>
                    <button id="fullPlayBtn" class="btn btn-link mx-3">
                        <i class="fas fa-play fa-2x"></i>
                    </button>
                    <button id="fullNextBtn" class="btn btn-link mx-3">
                        <i class="fas fa-step-forward fa-2x"></i>
                    </button>
                    <button id="repeatBtn" class="btn btn-link mx-3">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between">
                    <span id="fullCurrentTime">0:00</span>
                    <span id="fullDuration">0:00</span>
                </div>
            </div>
        </div>
    </div>
</div>