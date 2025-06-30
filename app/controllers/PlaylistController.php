<?php
class PlaylistController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function show($playlistId)
    {
        // error_log("Received request for playlist ID: " . $playlistId);

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit(json_encode(['error' => 'Unauthorized']));
        }

        $playlist = $this->userModel->getPlaylist($_SESSION['user_id'], $playlistId);

        if (!$playlist) {
            http_response_code(404);
            exit(json_encode(['error' => 'Playlist not found']));
        }

        header('Content-Type: application/json');
        echo json_encode($playlist);
        exit;
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        $playlistCount = $this->userModel->getUserPlaylistCount($_SESSION['user_id']);
        $newPlaylist = [
            'name' => 'MyPlaylist' . ($playlistCount + 1),
            'image_url' => URL_ROOT . '/assets/images/default-playlist-512px.png',
            'description' => ''
        ];

        $playlistId = $this->userModel->createPlaylist($_SESSION['user_id'], $newPlaylist);

        // Add to library
        $this->userModel->addToLibrary($_SESSION['user_id'], 'playlist', $playlistId, [
            'name' => $newPlaylist['name'],
            'image_url' => $newPlaylist['image_url']
        ]);

        // Return full playlist structure including empty songs array
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'id' => $playlistId,
            'name' => $newPlaylist['name'],
            'image_url' => $newPlaylist['image_url'],
            'songs' => [] // Explicit empty array
        ]);
        exit;
    }

    //======================
    //  PLAYLIST MODIFIERS  
    //======================
    public function addSong()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $playlistId = $data['playlistId'] ?? null;
        $songId = $data['songId'] ?? null;

        if (!$playlistId || !$songId) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        // Check if this is a default playlist
        if ($this->userModel->hasDefaultPlaylist($_SESSION['user_id']) == $playlistId) {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Default playlist cannot be modified']);
            exit;
        }

        $success = $this->userModel->addSongToPlaylist($_SESSION['user_id'], $playlistId, $songId);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Song added to playlist' : 'Failed to add song to playlist'
        ]);
        exit;
    }

    public function removeSong()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $playlistId = $data['playlistId'] ?? null;
        $songId = $data['songId'] ?? null;

        if (!$playlistId || !$songId) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        // Check if this is a default playlist
        if ($this->userModel->hasDefaultPlaylist($_SESSION['user_id']) == $playlistId) {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Default playlist cannot be modified']);
            exit;
        }

        $success = $this->userModel->removeSongFromPlaylist($_SESSION['user_id'], $playlistId, $songId);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Song removed from playlist' : 'Failed to remove song from playlist'
        ]);
        exit;
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $playlistId = $data['id'] ?? null;

        if (!$playlistId) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Missing playlist ID']);
            exit;
        }

        // Check if this is a default playlist
        if ($this->userModel->hasDefaultPlaylist($_SESSION['user_id']) == $playlistId) {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Default playlist cannot be modified']);
            exit;
        }

        if (!empty($_FILES['image'])) {
            // Handle file upload
            $uploadDir = 'public/assets/images/playlists/';
            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $data['image_url'] = URL_ROOT . '/assets/images/playlists/' . $filename;
            }
        }

        $success = $this->userModel->updatePlaylist($_SESSION['user_id'], $playlistId, $data);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Playlist updated' : 'Failed to update playlist'
        ]);
        exit;
    }
}
