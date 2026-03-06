<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CorsFilter implements FilterInterface
{
    private array $allowed = [
        'http://localhost:5173',
        'https://man-vez.vercel.app',
        'https://last-king.vercel.app',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $origin = $request->getHeaderLine('Origin');
        $response = service('response');

        if (in_array($origin, $this->allowed)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
        }
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');

        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $response->setStatusCode(200)->setBody('');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $origin = $request->getHeaderLine('Origin');

        if (in_array($origin, $this->allowed)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
        }
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}