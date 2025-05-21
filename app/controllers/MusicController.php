<?php
class MusicController extends BaseController
{
    private $musicModel;

    public function __construct()
    {
        $this->musicModel = new Music();
    }

    public function browse()
    {
        // Get popular tracks
        $tracks = $this->musicModel->getPopularTracks(20);

        $data = [
            'tracks' => $tracks['results'] ?? []
        ];

        $this->view('music/browse', $data);
    }

    public function search()
    {
        // Initialize default data
        $data = [
            'query' => '',
            'results' => [],
            'search_performed' => false,
            'filters' => [
                'genre' => '',
                'duration_min' => '',
                'duration_max' => '',
                'order' => 'popularity_total'
            ],
            'current_page' => 1,
            'total_pages' => 1,
            'suggestions' => []
        ];

        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize inputs
            $query = trim(htmlspecialchars($_POST['query'] ?? ''));
            $page = intval($_POST['page'] ?? 1);
            $filters = [
                'genre' => $_POST['genre'] ?? '',
                'duration_min' => intval($_POST['duration_min'] ?? 0),
                'duration_max' => intval($_POST['duration_max'] ?? 0),
                'order' => in_array($_POST['order'] ?? '', ['popularity_total', 'releasedate'])
                    ? $_POST['order'] : 'popularity_total'
            ];

            if (!empty($query)) {
                // Get search results with pagination (20 items per page)
                $results = $this->musicModel->searchTracks($query, 20, $page, $filters);

                // Prepare data for view
                $data = [
                    'query' => $query,
                    'results' => $results['results'] ?? [],
                    'search_performed' => true,
                    'filters' => $filters,
                    'current_page' => $page,
                    'total_pages' => min(10, ceil($results['headers']['results_count'] ?? 0) / 20),
                    'total_results' => $results['headers']['results_count'] ?? 0
                ];
            }
        }

        // Get search suggestions for empty search
        if (empty($data['query'])) {
            $data['suggestions'] = $this->musicModel->getSearchSuggestions();
        }

        $this->view('music/search', $data);
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
