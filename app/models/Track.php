<?php

class Track {

    private $clientId;
    private $jamendoApi;

    public function __construct() {
        $this->clientId = JAMENDO_CLIENT_ID;
        $this->jamendoApi = new JamendoApi();
    }

    // Get tracks by IDs
    public function getJamendoTracksById(array $ids) {

        if (empty($ids)) {
            error_log("Empty track IDs requested");
            return ['results' => []];
        }

        // Filter out empty/non-integer IDs
        $validIds = array_filter($ids, function ($id) {
            return !empty($id) && is_numeric($id) && intval($id) > 0;
        });

        if (empty($validIds)) {
            return ['results' => []];
        }

        // The Jamendo API doesn't support multiple IDs in a single request,
        // so we need to make separate requests for each ID.
        $allResults = [];
        foreach ($validIds as $id) {
            $url = JAMENDO_BASE_URL . '/tracks/?client_id=' . $this->clientId .
                '&format=jsonpretty&id=' . intval($id);
            error_log("Jamendo API Request: " . $url);
            $result = $this->jamendoApi->callApi($url);
            if (!empty($result['results'])) {
                $allResults = array_merge($allResults, $result['results']);
            }
        }

        return ['results' => $allResults];
    }

    // Get popular tracks through Jamendo API (return up to 10 by default)
    public function getJamendoPopularTracks($limit = 10) {
        $url = JAMENDO_BASE_URL . '/tracks/?client_id=' . $this->clientId .
            '&format=jsonpretty&limit=' . $limit . '&order=popularity_total';

        return $this->jamendoApi->callApi($url);
    }

    // Get tracks by search query through Jamendo API
    public function searchJamendoTracks($query, $limit = 12){
        $url = JAMENDO_BASE_URL . '/tracks/?client_id=' . $this->clientId .
            '&format=jsonpretty&limit=' . $limit .
            '&search=' . urlencode($query) .
            '&include=musicinfo&audiodl=1'; // Ensure audio download links

        $result = $this->jamendoApi->callApi($url);

        // Filter out tracks without audio
        if (isset($result['results'])) {
            $result['results'] = array_filter($result['results'], function ($track) {
                return !empty($track['audio']) || !empty($track['audiodownload']);
            });
        }

        return $result;
    }

}