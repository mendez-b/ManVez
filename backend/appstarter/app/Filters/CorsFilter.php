<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $originHeader = $request->getHeaderLine('Origin');
        $config = config('Cors');
        $conf = $config->default ?? [];

        $allowedOrigins = $conf['allowedOrigins'] ?? [];
        $allowedPatterns = $conf['allowedOriginsPatterns'] ?? [];
        $supportsCredentials = $conf['supportsCredentials'] ?? false;
        $allowedHeaders = $conf['allowedHeaders'] ?? ['Content-Type', 'Authorization', 'X-Requested-With'];
        $allowedMethods = $conf['allowedMethods'] ?? ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
        $maxAge = $conf['maxAge'] ?? 7200;

        $originToSet = null;
        if ($originHeader) {
            if (in_array($originHeader, $allowedOrigins, true)) {
                $originToSet = $originHeader;
            } else {
                foreach ($allowedPatterns as $pattern) {
                    if (preg_match('#\\A' . $pattern . '\\z#', $originHeader)) {
                        $originToSet = $originHeader;
                        break;
                    }
                }
            }
        }

        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            $res = service('response');
            if ($originToSet) {
                $res->setHeader('Access-Control-Allow-Origin', $originToSet);
                if ($supportsCredentials) {
                    $res->setHeader('Access-Control-Allow-Credentials', 'true');
                }
            }

            $reqHeaders = $request->getHeaderLine('Access-Control-Request-Headers');
            $res->setHeader('Access-Control-Allow-Headers', $reqHeaders ?: implode(', ', $allowedHeaders));
            $res->setHeader('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
            $res->setHeader('Access-Control-Max-Age', (string) $maxAge);

            return $res->setStatusCode(200);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $originHeader = $request->getHeaderLine('Origin');
        $config = config('Cors');
        $conf = $config->default ?? [];

        $allowedOrigins = $conf['allowedOrigins'] ?? [];
        $allowedPatterns = $conf['allowedOriginsPatterns'] ?? [];
        $supportsCredentials = $conf['supportsCredentials'] ?? false;
        $allowedHeaders = $conf['allowedHeaders'] ?? ['Content-Type', 'Authorization', 'X-Requested-With'];
        $allowedMethods = $conf['allowedMethods'] ?? ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
        $exposedHeaders = $conf['exposedHeaders'] ?? [];
        $maxAge = $conf['maxAge'] ?? 7200;

        $originToSet = null;
        if ($originHeader) {
            if (in_array($originHeader, $allowedOrigins, true)) {
                $originToSet = $originHeader;
            } else {
                foreach ($allowedPatterns as $pattern) {
                    if (preg_match('#\\A' . $pattern . '\\z#', $originHeader)) {
                        $originToSet = $originHeader;
                        break;
                    }
                }
            }
        }

        if ($originToSet) {
            $response->setHeader('Access-Control-Allow-Origin', $originToSet);
            if ($supportsCredentials) {
                $response->setHeader('Access-Control-Allow-Credentials', 'true');
            }
        }

        $response->setHeader('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        $response->setHeader('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        if (!empty($exposedHeaders)) {
            $response->setHeader('Access-Control-Expose-Headers', implode(', ', $exposedHeaders));
        }
        $response->setHeader('Access-Control-Max-Age', (string) $maxAge);

        return $response;
    }
}
