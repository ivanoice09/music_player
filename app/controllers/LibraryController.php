<?php
class LibraryController extends BaseController {

    private $libraryModel;
    private $playlistModel;

    public function __construct()
    {
        $this->playlistModel = new Playlist();
        $this->libraryModel = new Library();
    }

    // Putting library items inside library
    /**
     * MODIFICATION @14:08 JUNE 10 2025:
     * handles both cases - when the page is reloaded or accessed through the url directly,
     * the page goes automatically back to home page
     */
    public function index() {

        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            } else {
                // Redirect to login for full page requests
                header('Location: /login');
                exit;
            }
        }

        try {
            // Ensure default playlist exists
            if (!$this->playlistModel->hasDefaultPlaylist($_SESSION['user_id'])) {
                $created = $this->playlistModel->createDefaultPlaylist($_SESSION['user_id']);
                if (!$created) {
                    error_log("Failed to create default playlist for user: " . $_SESSION['user_id']);
                }
            }

            $libraryItems = $this->libraryModel->getLibrary($_SESSION['user_id']);

            if ($this->isAjaxRequest()) {
                // AJAX request - return JSON
                header('Content-Type: application/json');
                echo json_encode($libraryItems);
                exit;
            } else {
                // Full page request - load view with data
                $this->view('layouts/main', [
                    'is_music_page' => true,
                    'library_items' => $libraryItems
                ]);
            }
        } catch (Exception $e) {

            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
                exit;
                
            } else {
                // Handle error for full page request
                $this->view('layouts/main', [
                    'is_music_page' => true,
                    'error' => 'Server error: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function addItem()
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

        $result = $this->libraryModel->addToLibrary($_SESSION['user_id'], $type, $itemId, $metadata);

        echo json_encode([
            'success' => true,
            'item' => $result
        ]);
        exit;
    }

    public function removeItem()
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

        $success = $this->libraryModel->removeFromLibrary($_SESSION['user_id'], $itemId);

        echo json_encode([
            'success' => $success
        ]);
        exit;
    }

    public function pinItem()
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

        $success = $this->libraryModel->togglePinLibraryItem($_SESSION['user_id'], $itemId, $pinned);

        echo json_encode([
            'success' => $success,
            'pinned' => $pinned
        ]);
        exit;
    }
}
