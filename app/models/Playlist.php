<?php

class Playlist {

    private $db;
    private $trackModel;
    private $libraryModel;

    public function __construct() {
        $this->db = new Database();
        $this->trackModel = new Track();
        $this->libraryModel = new Library();
    }

    // helper function: check if default playlist already exists
    public function hasDefaultPlaylist($userId) {
        $stmt = $this->db->prepare("
            SELECT id FROM playlists 
            WHERE user_id = ? AND is_default = 1
            LIMIT 1
        ");
        $stmt->execute([$userId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : false;
    }

    // Creates the default playlist once the user registers in the application
    // like in Spotify each user has a default playlist called "Favorites"
    // which is created automatically.
    public function createDefaultPlaylist($userId) {

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
        $this->libraryModel->addToLibrary($userId, 'playlist', $playlistId, [
            'name' => $playlistData['name'],
            'image' => $playlistData['image_url'],
            'is_default' => true,
            'is_pinned' => true
        ]);

        return $playlistId;
    }

    // Add a new playlist to the database
    // This method will be used when the user clicks on "Create Playlist"
    public function createPlaylist($userId, $playlistData) {
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

    // Update an existing playlist
    // This method will be used to update the playlist details 
    // like name, image, and description.
    // It will also verify that the user owns the playlist before updating.
    // In the future I will add other specific methods to update the playlist
    // like a specific method to update the playlist image, name, or description.
    public function updatePlaylist($userId, $playlistId, $data) {
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

    // TODO: IMPLEMENT DELETE PLAYLIST

    // Get all playlists for a user
    // This method will return all playlists created by the user
    // including the default playlist if it exists.
    public function getUserPlaylists($userId) {
        $stmt = $this->db->prepare("SELECT * FROM playlists WHERE user_id = ?");
        $stmt->execute([$userId]);
        $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // For each playlist, add the song IDs
        foreach ($playlists as &$playlist) {
            $playlist['songs'][] = [];

            $stmtSongs = $this->db->prepare("SELECT song_id FROM playlist_songs WHERE playlist_id = ?");
            $stmtSongs->execute([$playlist['id']]);
            $songIds = $stmtSongs->fetchAll(PDO::FETCH_COLUMN, 0);

            $playlist['songs'] = $songIds;
        }

        return $playlists;
    }

    // Get a specific playlist by user ID and playlist ID
    // This method will return the playlist details along with the songs in it
    public function getPlaylist($userId, $playlistId) {
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
            error_log('Song IDs: ' . print_r($songIds, true));
            $tracks = $$this->trackModel->getJamendoTracksById($songIds);
            error_log('Tracks: ' . print_r($tracks, true));

            if (!empty($tracks['results'])) {
                foreach ($tracks['results'] as $song) {
                    $playlist['songs'][] = [
                        'id' => $song['id'],
                        'name' => $song['name'],
                        'artist_name' => $song['artist_name'],
                        'duration' => $song['duration'],
                        'image' => $song['image'],
                        'audio' => $song['audio'] ?? $song['audiodownload'] ?? null
                    ];
                }
            } else {
                error_log("No tracks found for IDs: " . implode(',', $songIds));
            }
        }

        return $playlist;
    }

    // Get the count of playlists for a user
    public function getUserPlaylistCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM playlists WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    // Add a track to a playlist
    public function addTrackToPlaylist($userId, $playlistId, $songId) {
        // First verify the user owns the playlist
        $stmt = $this->db->prepare("SELECT id FROM playlists WHERE user_id = ? AND id = ?");
        $stmt->execute([$userId, $playlistId]);

        if ($stmt->rowCount() === 0) {
            return false;
        }

        // Check if track already exists in playlist
        $stmt = $this->db->prepare("SELECT id FROM playlist_songs WHERE playlist_id = ? AND song_id = ?");
        $stmt->execute([$playlistId, $songId]);

        if ($stmt->rowCount() > 0) {
            return false; // Song already in playlist
        }

        // Add track to playlist
        $stmt = $this->db->prepare("INSERT INTO playlist_songs (playlist_id, song_id) VALUES (?, ?)");
        return $stmt->execute([$playlistId, $songId]);
    }

    // Remove a track from a playlist
    public function removeTrackFromPlaylist($userId, $playlistId, $songId){
        // First verify the user owns the playlist
        $stmt = $this->db->prepare("SELECT id FROM playlists WHERE user_id = ? AND id = ?");
        $stmt->execute([$userId, $playlistId]);

        if ($stmt->rowCount() === 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM playlist_songs WHERE playlist_id = ? AND song_id = ?");
        return $stmt->execute([$playlistId, $songId]);
    }

}