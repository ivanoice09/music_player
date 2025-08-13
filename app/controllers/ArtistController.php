<?php
class ArtistController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function show($artistId)
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit(json_encode(['error' => 'Unauthorized']));
        }

        $artist = $this->userModel->getArtist($_SESSION['user_id'], $artistId);

        if (!$artist) {
            http_response_code(404);
            exit(json_encode(['error' => 'Artist not found']));
        }

        header('Content-Type: application/json');
        echo json_encode($artist);
        exit;
    }
}
