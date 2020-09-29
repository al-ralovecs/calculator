<?php declare(strict_types=1);

namespace App\Middleware;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Middleware\CorsMiddleware;
use Laminas\Diactoros\Response\JsonResponse;

class CorsMiddlewareFactory
{
	/**
	 * @param ContainerInterface $container
	 * @return CorsMiddleware
	 */
	public function __invoke(ContainerInterface $container): CorsMiddleware
	{
		$origins = [
		    'http://localhost:8080',
            'http://localhost:8081',
		];
		
		$methods = [
			RequestMethodInterface::METHOD_OPTIONS,
			RequestMethodInterface::METHOD_GET,
			RequestMethodInterface::METHOD_POST,
		];
		
		$error = function(
			RequestInterface $request,
			ResponseInterface $response,
			$arguments
		) {
			return new JsonResponse($arguments, 403);
		};
		
		$headers = [
			'Content-Type',
			'Accept',
			'Authorization',
		];
		
		$params = [
			'origin' => $origins,
			'methods' => $methods,
			'headers.allow' => $headers,
			'headers.expose' => [],
			'credentials' => true,
			'cache' => 0,
			'error' => $error,
		];
		
		return new CorsMiddleware($params);
	}
}
