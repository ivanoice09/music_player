<?php
class Music
{
    private $clientId;

    public function __construct()
    {
        $this->clientId = JAMENDO_CLIENT_ID;
    }

    // Call Jamendo API
    private function callApi($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    // Get popular tracks
    public function getPopularTracks($limit = 10)
    {
        $url = JAMENDO_BASE_URL . '/tracks/?client_id=' . $this->clientId .
            '&format=jsonpretty&limit=' . $limit . '&order=popularity_total';

        return $this->callApi($url);
    }

    // Get track by ID
    public function getTrack($id)
    {
        $url = JAMENDO_BASE_URL . '/tracks/?client_id=' . $this->clientId .
            '&format=jsonpretty&id=' . $id;

        return $this->callApi($url);
    }

    public function searchTracks($query, $limit = 12)
    {
        $url = JAMENDO_BASE_URL . '/tracks/?client_id=' . $this->clientId .
            '&format=jsonpretty&limit=' . $limit .
            '&search=' . urlencode($query) .
            '&include=musicinfo&audiodl=1'; // Ensure audio download links

        $result = $this->callApi($url);

        // Filter out tracks without audio
        if (isset($result['results'])) {
            $result['results'] = array_filter($result['results'], function ($track) {
                return !empty($track['audio']) || !empty($track['audiodownload']);
            });
        }

        return $result;
    }

    public function getPopularArtists($limit = 3)
    {
        $url = JAMENDO_BASE_URL . '/artists/?client_id=' . $this->clientId .
            '&format=jsonpretty&limit=' . $limit . '&order=popularity_total';

        $result = $this->callApi($url);
        return array_map(function ($artist) {
            return [
                'id' => $artist['id'],
                'name' => $artist['name'],
                'image' => $artist['image'] ?? 'default-artist.jpg'
            ];
        }, $result['results'] ?? []);
    }

    public function getPopularAlbums($limit = 3)
    {
        $url = JAMENDO_BASE_URL . '/albums/?client_id=' . $this->clientId .
            '&format=jsonpretty&limit=' . $limit . '&order=popularity_total';

        $result = $this->callApi($url);
        return array_map(function ($album) {
            return [
                'id' => $album['id'],
                'name' => $album['name'],
                'image' => $album['image'],
                'artist_name' => $album['artist_name']
            ];
        }, $result['results'] ?? []);
    }

    public function getArtistAlbums($artistId)
    {
        $url = JAMENDO_BASE_URL . '/albums/?client_id=' . $this->clientId .
            '&format=jsonpretty&artist_id=' . $artistId;

        return $this->callApi($url);
    }
}
