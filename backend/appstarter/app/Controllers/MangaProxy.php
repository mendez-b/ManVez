<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MangaProxy extends Controller
{
    private $baseUrl = 'https://api.mangadex.org';

    // Proxy para la API
    public function api()
    {
        $path = $this->request->getGet('path');
        $query = $this->request->getGet('query');
        
        $url = $this->baseUrl . $path;
        if ($query) $url .= '?' . $query;

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($url, [
                'headers' => ['Accept' => 'application/json']
            ]);
            
            return $this->response
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Content-Type', 'application/json')
                ->setBody($response->getBody());
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

    // Proxy para las portadas
    public function cover($mangaId, $fileName)
    {
        $url = "https://uploads.mangadex.org/covers/{$mangaId}/{$fileName}";
        
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->get($url);
            $contentType = $response->getHeader('content-type');
            
            return $this->response
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Content-Type', $contentType)
                ->setBody($response->getBody());
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('Imagen no encontrada');
        }
    }
}