<?php
class User
{
    private $db;
    private $musicModel;
    private $playlistModel;

    public function __construct()
    {
        $this->db = new Database();
        $this->playlistModel = new Playlist();
    }

    // Register user
    public function register($data)
    {
        $this->db->query('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        if ($this->db->execute()) {
            // Get the new user's ID
            $userId = $this->db->lastInsertId();

            // Create default playlist
            $this->playlistModel->createDefaultPlaylist($userId);

            return true;
        } else {
            return false;
        }
    }

    // Login user
    public function login($username, $password)
    {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row->password;
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        return false;
    }

    // Find user by email (Very Important for authentication)
    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $this->db->single();

        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Find user by username (Very Important for authentication)
    public function findUserByUsername($username)
    {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        $this->db->single();

        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //=====================
    // ARTIST FUNCTIONALITY
    //=====================
    public function getArtist($userId, $artistId)
    {
        // First verify the artist exists in user's library
        $stmt = $this->db->prepare("
        SELECT * FROM library_items 
        WHERE user_id = ? AND item_type = 'artist' AND item_id = ?
    ");
        $stmt->execute([$userId, $artistId]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        // Get artist info from Jamendo
        $artist = $this->musicModel->getArtist($artistId);

        if (!$artist) {
            return null;
        }

        // Get artist's popular songs
        $songs = $this->musicModel->getArtistTracks($artistId, 20); // Limit to 20 songs

        return [
            'id' => $artist['id'],
            'name' => $artist['name'],
            'image' => $artist['image'],
            'songs' => $songs['results'] ?? []
        ];
    }

    //====================
    // ALBUM FUNCTIONALITY
    //====================
    public function getAlbum($userId, $albumId)
    {
        // First verify the album exists in user's library
        $stmt = $this->db->prepare("
        SELECT * FROM library_items 
        WHERE user_id = ? AND item_type = 'album' AND item_id = ?
    ");
        $stmt->execute([$userId, $albumId]);

        if ($stmt->rowCount() === 0) {
            return null;
        }

        // Get album info from Jamendo
        $album = $this->musicModel->getAlbum($albumId);

        if (!$album) {
            return null;
        }

        // Get album tracks
        $songs = $this->musicModel->getAlbumTracks($albumId);

        return [
            'id' => $album['id'],
            'name' => $album['name'],
            'image' => $album['image'],
            'artist_name' => $album['artist_name'],
            'songs' => $songs['results'] ?? []
        ];
    }
}
