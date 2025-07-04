<?php
class User
{
    private $db;
    private $musicModel;

    public function __construct()
    {
        $this->db = new Database();
        $this->musicModel = new Music();
    }

    //=========================
    // AUTHENTICATION FUNCTIONS
    //=========================
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
            $this->createDefaultPlaylist($userId);

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

    // Find user by email
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

    // Find user by username
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

    //======================
    // LIBRARY FUNCTIONALITY
    //======================
    // Add these methods to your User model
    public function getLibrary($userId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                li.id,
                li.item_type,
                li.item_id,
                li.metadata,
                li.is_pinned,
                li.created_at,
                CASE 
                    WHEN li.item_type = 'playlist' THEN p.name
                    ELSE JSON_UNQUOTE(JSON_EXTRACT(li.metadata, '$.name'))
                END as name,
                CASE 
                    WHEN li.item_type = 'playlist' THEN p.image_url
                    ELSE JSON_UNQUOTE(JSON_EXTRACT(li.metadata, '$.image'))
                END as image,
                p.is_default
            FROM library_items li
            LEFT JOIN playlists p ON li.item_type = 'playlist' AND li.item_id = p.id
            WHERE li.user_id = ?
            ORDER BY li.is_pinned DESC, li.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToLibrary($userId, $type, $itemId, $metadata = [])
    {
        $stmt = $this->db->prepare("
        INSERT INTO library_items 
        (user_id, item_type, item_id, metadata, is_pinned) 
        VALUES (?, ?, ?, ?, ?)
    ");

        $isPinned = ($type === 'playlist' && isset($metadata['is_default']))
            ? 1 : 0;

        $stmt->execute([
            $userId,
            $type,
            $itemId,
            json_encode($metadata),
            $isPinned
        ]);

        return [
            'id' => $this->db->lastInsertId(),
            'user_id' => $userId,
            'item_type' => $type,
            'item_id' => $itemId,
            'metadata' => $metadata,
            'is_pinned' => $isPinned
        ];
    }

    public function removeFromLibrary($userId, $itemId)
    {
        $stmt = $this->db->prepare("
        DELETE FROM library_items 
        WHERE user_id = ? AND id = ?
    ");
        return $stmt->execute([$userId, $itemId]);
    }

    public function togglePinLibraryItem($userId, $itemId, $pinned)
    {
        $stmt = $this->db->prepare("
        UPDATE library_items 
        SET is_pinned = ?
        WHERE user_id = ? AND id = ?
    ");
        return $stmt->execute([$pinned ? 1 : 0, $userId, $itemId]);
    }

    //=======================
    // PLAYLIST FUNCTIONALITY
    //=======================
    // helper function: check if default playlist already exists
    public function hasDefaultPlaylist($userId)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM playlists 
            WHERE user_id = ? AND is_default = 1
            LIMIT 1
        ");
        $stmt->execute([$userId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : false;
    }

    // creates the default playlist once the user registers in the application
    public function createDefaultPlaylist($userId)
    {

        // Check if default playlist already exists
        if ($this->hasDefaultPlaylist($userId)) {
            return false;
        }

        // Create default playlist
        $playlistData = [
            'name' => 'Favorites',
            'image_url' => URL_ROOT . '/assets/images/favourite-playlist-512px.png',
            'description' => 'Your favorite tracks',
            'is_default' => 1
        ];

        $stmt = $this->db->prepare("
            INSERT INTO playlists 
            (user_id, name, image_url, description, is_default) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $playlistData['name'],
            $playlistData['image_url'],
            $playlistData['description'],
            1 // is_default
        ]);

        $playlistId = $this->db->lastInsertId();

        // Add to library
        $this->addToLibrary($userId, 'playlist', $playlistId, [
            'name' => $playlistData['name'],
            'image' => $playlistData['image_url'],
            'is_default' => true,
            'is_pinned' => true
        ]);

        return $playlistId;
    }

    public function getUserPlaylistCount($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM playlists WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function getPlaylist($userId, $playlistId)
    {
        // 1. Verify playlist exists and belongs to user
        $stmt = $this->db->prepare("
            SELECT * FROM playlists 
            WHERE user_id = ? AND id = ?
        ");
        $stmt->execute([$userId, $playlistId]);
        $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$playlist) {
            error_log("Playlist not found or access denied");
            return null;
        }

        // 2. Initialize songs array
        $playlist['songs'] = [];

        // 3. Get song IDs only if playlist exists
        $stmt = $this->db->prepare("
            SELECT song_id FROM playlist_songs 
            WHERE playlist_id = ?
            ORDER BY added_at
        ");
        $stmt->execute([$playlistId]);
        $songIds = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        // 4. Fetch track details only if we have song IDs
        if (!empty($songIds)) {
            $music = new Music();
            $tracks = $music->getTracks($songIds);

            if (!empty($tracks['results'])) {
                foreach ($tracks['results'] as $song) {
                    $playlist['songs'][] = [
                        'id' => $song['id'],
                        'title' => $song['name'],
                        'artist' => $song['artist_name'],
                        'duration' => $song['duration'],
                        'image_url' => $song['image'],
                        'audio_url' => $song['audio'] ?? $song['audiodownload'] ?? null
                    ];
                }
            } else {
                error_log("No tracks found for IDs: " . implode(',', $songIds));
            }
        }

        return $playlist;
    }

    public function createPlaylist($userId, $playlistData)
    {
        $stmt = $this->db->prepare("
        INSERT INTO playlists (user_id, name, image_url, description)
        VALUES (?, ?, ?, ?)
    ");
        $stmt->execute([
            $userId,
            $playlistData['name'],
            $playlistData['image_url'],
            $playlistData['description'] ?? null
        ]);

        return $this->db->lastInsertId();
    }

    public function addSongToPlaylist($userId, $playlistId, $songId)
    {
        // First verify the user owns the playlist
        $stmt = $this->db->prepare("SELECT id FROM playlists WHERE user_id = ? AND id = ?");
        $stmt->execute([$userId, $playlistId]);

        if ($stmt->rowCount() === 0) {
            return false;
        }

        // Check if song already exists in playlist
        $stmt = $this->db->prepare("SELECT id FROM playlist_songs WHERE playlist_id = ? AND song_id = ?");
        $stmt->execute([$playlistId, $songId]);

        if ($stmt->rowCount() > 0) {
            return false; // Song already in playlist
        }

        // Add song to playlist
        $stmt = $this->db->prepare("INSERT INTO playlist_songs (playlist_id, song_id) VALUES (?, ?)");
        return $stmt->execute([$playlistId, $songId]);
    }

    public function removeSongFromPlaylist($userId, $playlistId, $songId)
    {
        // First verify the user owns the playlist
        $stmt = $this->db->prepare("SELECT id FROM playlists WHERE user_id = ? AND id = ?");
        $stmt->execute([$userId, $playlistId]);

        if ($stmt->rowCount() === 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM playlist_songs WHERE playlist_id = ? AND song_id = ?");
        return $stmt->execute([$playlistId, $songId]);
    }

    public function updatePlaylist($userId, $playlistId, $data)
    {
        // First verify the user owns the playlist
        $stmt = $this->db->prepare("SELECT id FROM playlists WHERE user_id = ? AND id = ?");
        $stmt->execute([$userId, $playlistId]);

        if ($stmt->rowCount() === 0) {
            return false;
        }

        $stmt = $this->db->prepare("
        UPDATE playlists 
        SET name = ?, image_url = ?, description = ?
        WHERE id = ?
    ");
        return $stmt->execute([
            $data['name'],
            $data['image_url'],
            $data['description'] ?? null,
            $playlistId
        ]);
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
