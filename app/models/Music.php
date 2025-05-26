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
}
