<!-- Global Player Bar -->
<div id="globalPlayer" class="fixed-bottom bg-dark text-white" style="display: none;">
    <div class="container-fluid">
        <div class="row align-items-center py-2">
            <!-- Track Info -->
            <div class="col-md-3 d-flex align-items-center">
                <img id="playerArtwork" src="" class="img-thumbnail mr-3" style="width: 50px; height: 50px; display: none;">
                <div>
                    <div id="playerTrackTitle" class="font-weight-bold">No track selected</div>
                    <div id="playerTrackArtist" class="text-muted small"></div>
                </div>
            </div>

            <!-- Player Controls -->
            <div class="col-md-6">
                <div class="d-flex justify-content-center align-items-center">
                    <button id="prevBtn" class="btn btn-link text-white mx-2">
                        <i class="fas fa-step-backward"></i>
                    </button>
                    <button id="playPauseBtn" class="btn btn-link text-white mx-2">
                        <i class="fas fa-play"></i>
                    </button>
                    <button id="nextBtn" class="btn btn-link text-white mx-2">
                        <i class="fas fa-step-forward"></i>
                    </button>
                </div>
                <div class="progress mt-2">
                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span id="currentTime">0:00</span>
                    <span id="duration">0:00</span>
                </div>
            </div>

            <!-- Volume Control -->
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-volume-down text-muted mr-2"></i>
                    <input type="range" id="volumeControl" class="custom-range" min="0" max="1" step="0.01" value="0.7">
                    <i class="fas fa-volume-up text-muted ml-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Audio Element -->
    <audio id="audioElement"></audio>
</div>