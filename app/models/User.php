<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Register user
    public function register($data)
    {
        $this->db->query('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        if ($this->db->execute()) {
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
        // For MySQL/MariaDB versions that support JSON functions
        $stmt = $this->db->prepare("
        SELECT li.*, 
            CASE 
                WHEN li.item_type = 'playlist' THEN p.name
                ELSE JSON_UNQUOTE(JSON_EXTRACT(li.metadata, '$.name'))
            END as name,
            CASE 
                WHEN li.item_type = 'playlist' THEN p.image_url
                ELSE JSON_UNQUOTE(JSON_EXTRACT(li.metadata, '$.image'))
            END as image
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
    public function getUserPlaylistCount($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM playlists WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function getPlaylist($userId, $playlistId)
    {
        $stmt = $this->db->prepare("
                SELECT p.*, 
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', s.id,
                            'title', s.title,
                            'artist', s.artist_name,
                            'duration', s.duration,
                            'image_url', s.image_url,
                            'audio_url', s.audio_url
                        )
                    ) as songs
                FROM playlists p
                LEFT JOIN playlist_songs ps ON p.id = ps.playlist_id
                LEFT JOIN songs s ON ps.song_id = s.id
                WHERE p.user_id = ? AND p.id = ?
                GROUP BY p.id
            ");
        $stmt->execute([$userId, $playlistId]);
        $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($playlist) {
            $playlist['songs'] = json_decode($playlist['songs'] ?? '[]', true);
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
}
