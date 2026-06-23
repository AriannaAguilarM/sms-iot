<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CorsFilter
 * 
 * Permite solicitudes HTTP desde el ESP32, el dashboard web y
 * herramientas como Postman o Thunder Client.
 * 
 * Para producción académica se permite '*'. En producción real
 * deberías limitar a tu dominio específico.
 */
class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): void
    {
        // Permite solicitudes OPTIONS (preflight) del navegador
        if ($request->getMethod() === 'options') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            exit(0);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        return $response
            ->setHeader('Access-Control-Allow-Origin',  '*')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}