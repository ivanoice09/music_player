<?php
class MusicController extends BaseController
{
    private $musicModel;

    public function __construct()
    {
        $this->musicModel = new Music();
    }

    // Gets the songs that have been queried
    public function getSearchedSongs()
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

    // Gets popular songs for home page
    public function getPopularSongs()
    {
        // Get results from API
        $apiResults = $this->musicModel->getPopularTracks(20);

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

    public function loadView()
    {
        // Get search query if it exists
        $query = $_GET['q'] ?? '';

        // Prepare data for the view
        $data = [
            'is_music_page' => true,
            'query' => htmlspecialchars($query),
            'search_performed' => !empty($query)
        ];

        $this->view('layouts/main', $data);
    }

    public function loadTemplate()
    {
        $template = $_GET['name'] ?? 'song-grid';
        $templatePath = APP_ROOT . "/app/views/templates/{$template}.php";

        if (file_exists($templatePath)) {
            header('Content-Type: text/html');
            readfile($templatePath);
            exit;
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Template not found.";
        }
    }
}
