<?php
class GoogleBooks {
    private $apiBase = 'https://www.googleapis.com/books/v1/volumes';
    public function search($q, $maxResults = 12) {
        $params = http_build_query(['q' => $q, 'maxResults' => $maxResults]);
        $url = $this->apiBase . '?' . $params;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($res, true);
        return $data['items'] ?? [];
    }
    public function getById($googleId) {
        $url = $this->apiBase . '/' . urlencode($googleId);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }
}
