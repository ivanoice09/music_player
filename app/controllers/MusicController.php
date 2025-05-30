<?php
class MusicController extends BaseController
{
    private $musicModel;

    public function __construct()
    {
        $this->musicModel = new Music();
    }

    // Gets the songs that have been queried
    public function searchResults()
    {
        if (isset($_GET['q']) && !empty($_GET['q'])) {
            $query = $_GET['q'];
            $sanitizedQuery = trim(htmlspecialchars($query));

            // Get results from API
            $apiResults = $this->musicModel->searchTracks($sanitizedQuery, 20);

            // Transform the data to match what your JavaScript expects
            $transformedResults = [];
            if (isset($apiResults['results'])) {
                foreach ($apiResults['results'] as $track) {
                    $transformedResults[] = [
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

    //  Just for testing purposes: 
    // I want Js to render the display of the songs so whenever I change things
    // it happens on the spot, all of this happens on this function
    public function getPopularSongs()
    {
        // Get results from API
        $apiResults = $this->musicModel->getPopularTracks(20);

        // Transform the data to match what your JavaScript expects
        $transformedResults = [];
        if (isset($apiResults['results'])) {
            foreach ($apiResults['results'] as $track) {
                $transformedResults[] = [
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

    // Show results in search.php
    public function searchView()
    {
        // Get search query if it exists
        $query = $_GET['q'] ?? '';

        // Prepare data for the view
        $data = [
            'query' => htmlspecialchars($query),
            'search_performed' => !empty($query)
        ];

        $this->view('music/search', $data);
    }
}
