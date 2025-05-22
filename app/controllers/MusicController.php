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

    public function getSuggestions()
    {
        $query = $_GET['q'] ?? '';
        if (!empty($query)) {
            $results = $this->musicModel->searchTracks($query, 5);
            $suggestions = array_map(function ($track) {
                return [
                    'name' => $track['name'],
                    'artist' => $track['artist_name']
                ];
            }, $results['results'] ?? []);

            header('Content-Type: application/json');
            echo json_encode($suggestions);
            exit();
        }

        header('Content-Type: application/json');
        echo json_encode([]);
        exit();
    }

    public function instantSearch()
    {
        // Verify AJAX request
        if (
            empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'
        ) {
            http_response_code(403);
            exit(json_encode(['error' => 'Direct access forbidden']));
        }

        $query = $_GET['q'] ?? '';
        $limit = 12;

        try {
            $results = $this->musicModel->searchTracks($query, $limit);

            // Ensure we have valid audio URLs
            if (isset($results['results'])) {
                foreach ($results['results'] as &$track) {
                    if (empty($track['audio']) && !empty($track['audiodownload'])) {
                        $track['audio'] = $track['audiodownload'];
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'results' => $results['results'] ?? []
            ]);
            exit();
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode([
                'success' => false,
                'error' => 'Search failed: ' . $e->getMessage()
            ]);
            exit();
        }
    }
}
