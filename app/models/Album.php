<?php
class Album
{
    private $clientId;
    private $jamendoApi;

    public function __construct()
    {
        $this->clientId = JAMENDO_CLIENT_ID;
        $this->jamendoApi = new JamendoApi();
    }

    public function getAlbum($albumId)
    {
        $url = "https://api.jamendo.com/v3.0/albums/?client_id={$this->clientId}&id={$albumId}&format=json";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['results'][0])) {
            return $data['results'][0];
        }

        return null;
    }

    public function getAlbumTracks($albumId)
    {
        $url = "https://api.jamendo.com/v3.0/tracks/?client_id={$this->clientId}&album_id={$albumId}&format=json";
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    public function getArtistAlbums($artistId)
    {
        $url = JAMENDO_BASE_URL . '/albums/?client_id=' . $this->clientId .
            '&format=jsonpretty&artist_id=' . $artistId;

        return $this->jamendoApi->callApi($url);
    }

    public function getPopularAlbums($limit = 3)
    {
        $url = JAMENDO_BASE_URL . '/albums/?client_id=' . $this->clientId .
            '&format=jsonpretty&limit=' . $limit . '&order=popularity_total';

        $result = $this->jamendoApi->callApi($url);
        return array_map(function ($album) {
            return [
                'id' => $album['id'],
                'name' => $album['name'],
                'image' => $album['image'],
                'artist_name' => $album['artist_name']
            ];
        }, $result['results'] ?? []);
    }
}
