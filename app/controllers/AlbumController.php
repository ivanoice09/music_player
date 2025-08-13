<?php
class AlbumController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function show($albumId)
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit(json_encode(['error' => 'Unauthorized']));
        }

        $album = $this->userModel->getAlbum($_SESSION['user_id'], $albumId);

        if (!$album) {
            http_response_code(404);
            exit(json_encode(['error' => 'Album not found']));
        }

        header('Content-Type: application/json');
        echo json_encode($album);
        exit;
    }
}
