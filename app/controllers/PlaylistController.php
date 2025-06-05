<?php
class PlaylistController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function view($playlistId, $data = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }

        $playlist = $this->userModel->getPlaylist($_SESSION['user_id'], $playlistId);

        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode($playlist);
            exit;
        } else {
            $this->view('templates/playlist-view', ['playlist' => $playlist]);
        }
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
            'image_url' => URL_ROOT . '/assets/images/default-playlist.png'
        ];

        $playlistId = $this->userModel->createPlaylist($_SESSION['user_id'], $newPlaylist);

        echo json_encode([
            'success' => true,
            'id' => $playlistId
        ]);
        exit;
    }

    // ... other methods (addSong, removeSong, update)
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
