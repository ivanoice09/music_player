<?php
class LibraryController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // Putting library items inside library
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
