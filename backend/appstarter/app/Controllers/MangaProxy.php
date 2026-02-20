<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MangaProxy extends Controller
{
    private $baseUrl = 'https://api.mangadex.org';

    public function api()
    {
        $path = $this->request->getGet('path');
        $query = $this->request->getGet('query');
        
        $url = $this->baseUrl . $path;
        if ($query) $url .= '?' . $query;

        $result = $this->curlGet($url);
        
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Content-Type', 'application/json')
            ->setBody($result);
    }

    public function cover($mangaId, $fileName)
    {
        $url = "https://uploads.mangadex.org/covers/{$mangaId}/{$fileName}";
        $result = $this->curlGet($url, true);
        
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Content-Type', 'image/jpeg')
            ->setBody($result);
    }

    private function curlGet($url, $binary = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, [
         'Accept: application/json',
         'User-Agent: ManVez/1.0'
         ]);
        if ($binary) curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}