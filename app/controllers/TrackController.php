<?php

class TrackController {

    private $trackModel;

    public function __construct() {
        $this->trackModel = new Track();
    }

    // fetch and show popular tracks
    public function getAndShowPopularTracks() {

        // Get the results from Jamendo API. Limit up to 20 songs
        $apiResults = $this->trackModel->getJamendoPopularTracks(20);

        // Transform the data to match what your JavaScript expects
        $transformedResults = [];
        if (isset($apiResults['results'])) {
            foreach ($apiResults['results'] as $track) {
                $transformedResults[] = [
                    'id' => $track['id'] ?? $track['track_id'] ?? $track['song_id'] ?? '', // <-- Add this line
                    'audio' => $track['audio'] ?? $track['audiodownload'] ?? '',
                    'name' => $track['name'] ?? 'Unknown Track',
                    'artist_name' => $track['artist_name'] ?? 'Unknown Artist',
                    'image' => $track['image'] ?? 'default-image.jpg'
                ];
            }
        }

        // Set proper JSON header
        header('Content-Type: application/json');
        echo json_encode($transformedResults);
        exit;
    }

    // fetch and show searched tracks
    public function getAndShowSearchedTracks() {
        
        if (isset($_GET['q']) && !empty($_GET['q'])) {
            $query = $_GET['q'];
            $sanitizedQuery = trim(htmlspecialchars($query));

            // Get results from API
            $apiSearchResults = $this->trackModel->searchJamendoTracks($sanitizedQuery, 100);

            // Transform the data to match what your JavaScript expects
            $transformedResults = [];
            if (isset($apiSearchResults['results'])) {
                foreach ($apiSearchResults['results'] as $track) {
                    $transformedResults[] = [
                        'id' => $track['id'] ?? $track['track_id'] ?? $track['song_id'] ?? '', // <-- Add this line
                        'audio' => $track['audio'] ?? $track['audiodownload'] ?? '',
                        'name' => $track['name'] ?? 'Unknown Track',
                        'artist_name' => $track['artist_name'] ?? 'Unknown Artist',
                        'image' => $track['image'] ?? 'default-image.jpg'
                    ];
                }
            }

            // Set proper JSON header
            header('Content-Type: application/json');
            echo json_encode($transformedResults);
            exit;
        }
    }

}