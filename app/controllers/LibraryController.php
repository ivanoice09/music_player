<?php
class LibraryController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        try {
            // Ensure default playlist exists
            if (!$this->userModel->hasDefaultPlaylist($_SESSION['user_id'])) {
                $created = $this->userModel->createDefaultPlaylist($_SESSION['user_id']);
                if (!$created) {
                    error_log("Failed to create default playlist for user: " . $_SESSION['user_id']);
                }
            }

            $libraryItems = $this->userModel->getLibrary($_SESSION['user_id']);
            error_log(print_r($libraryItems, true)); // Log the retrieved items

            header('Content-Type: application/json');
            echo json_encode($libraryItems);
            exit;
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
            exit;
        }
    }

    // This means I want to put some initial contents inside the library to test if it works
    // private function seedInitialLibrary($userId)
    // {
    //     // Get sample content from Jamendo
    //     $jamendoContent = [
    //         'artists' => $this->musicModel->getPopularArtists(3),
    //         'albums' => $this->musicModel->getPopularAlbums(3),
    //         'playlists' => [
    //             [
    //                 'id' => 'default-1',
    //                 'name' => 'Favorites',
    //                 'image' => URL_ROOT . '/assets/images/playlist/favourite-playlist-512px.png',
    //                 'is_default' => true,
    //                 'is_pinned' => true
    //             ]
    //         ]
    //     ];

    //     $libraryItems = [];

    //     // Add artists
    //     foreach ($jamendoContent['artists'] as $artist) {
    //         $libraryItems[] = $this->userModel->addToLibrary($userId, 'artist', $artist['id'], [
    //             'name' => $artist['name'],
    //             'image' => $artist['image']
    //         ]);
    //     }

    //     // Add albums
    //     foreach ($jamendoContent['albums'] as $album) {
    //         $libraryItems[] = $this->userModel->addToLibrary($userId, 'album', $album['id'], [
    //             'name' => $album['name'],
    //             'image' => $album['image'],
    //             'artist' => $album['artist_name']
    //         ]);
    //     }

    //     // Add default playlist
    //     foreach ($jamendoContent['playlists'] as $playlist) {
    //         $libraryItems[] = $this->userModel->addToLibrary($userId, 'playlist', $playlist['id'], [
    //             'name' => $playlist['name'],
    //             'image_url' => $playlist['image'],
    //             'is_default' => $playlist['is_default'],
    //             'is_pinned' => $playlist['is_pinned']
    //         ]);
    //     }

    //     return $libraryItems;
    // }

    public function add()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? '';
        $itemId = $input['itemId'] ?? '';
        $metadata = $input['metadata'] ?? [];

        if (empty($type) || empty($itemId)) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        $result = $this->userModel->addToLibrary($_SESSION['user_id'], $type, $itemId, $metadata);

        echo json_encode([
            'success' => true,
            'item' => $result
        ]);
        exit;
    }

    public function remove()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $itemId = $input['itemId'] ?? '';

        if (empty($itemId)) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(['error' => 'Missing item ID']);
            exit;
        }

        $success = $this->userModel->removeFromLibrary($_SESSION['user_id'], $itemId);

        echo json_encode([
            'success' => $success
        ]);
        exit;
    }

    public function pin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $itemId = $input['itemId'] ?? '';
        $pinned = $input['pinned'] ?? true;

        if (empty($itemId)) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(['error' => 'Missing item ID']);
            exit;
        }

        $success = $this->userModel->togglePinLibraryItem($_SESSION['user_id'], $itemId, $pinned);

        echo json_encode([
            'success' => $success,
            'pinned' => $pinned
        ]);
        exit;
    }
}
