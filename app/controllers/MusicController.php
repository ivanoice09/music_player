<?php
class MusicController extends BaseController
{
    private $musicModel;

    public function __construct()
    {
        $this->musicModel = new Music();
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize search query
            $query = trim(htmlspecialchars($_POST['query']));

            if (!empty($query)) {
                // Get search results from Jamendo API
                $results = $this->musicModel->searchTracks($query, 20);

                $data = [
                    'query' => $query,
                    'results' => $results['results'] ?? [],
                    'search_performed' => true
                ];

                $this->view('music/search', $data);
            } else {
                // If search query is empty, redirect to browse
                redirect('music/browse');
            }
        } else {
            // If not POST request, show empty search page
            $data = [
                'query' => '',
                'results' => [],
                'search_performed' => false
            ];
            $this->view('music/search', $data);
        }
    }

    // new function: autoSearch() when I search the results show immediately
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
