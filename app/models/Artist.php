<?php

class Artist {

    private $clientId;
    private $jamendoApi;

    public function __construct(){
        $this->clientId = JAMENDO_CLIENT_ID;
        $this->jamendoApi = new JamendoApi();
    }

    public function getArtist($artistId) {
        $url = "https://api.jamendo.com/v3.0/artists/?client_id={$this->clientId}&id={$artistId}&format=json";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['results'][0])) {
            return $data['results'][0];
        }

        return null;
    }

    public function getArtistTracks($artistId, $limit = 20) {
        $url = "https://api.jamendo.com/v3.0/tracks/?client_id={$this->clientId}&artist_id={$artistId}&limit={$limit}&format=json";
        $response = file_get_contents($url);
        return json_decode($response, true);

    }

    public function getPopularArtists($limit = 3) {
        $url = JAMENDO_BASE_URL . '/artists/?client_id=' . $this->clientId .
            '&format=jsonpretty&limit=' . $limit . '&order=popularity_total';

        $result = $this->jamendoApi->callApi($url);
        return array_map(function ($artist) {
            return [
                'id' => $artist['id'],
                'name' => $artist['name'],
                'image' => $artist['image'] ?? 'default-artist.jpg'
            ];
        }, $result['results'] ?? []);
    }
    
}