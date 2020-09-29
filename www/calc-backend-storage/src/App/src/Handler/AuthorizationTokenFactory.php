<?php declare(strict_types=1);

namespace App\Handler;

use App\Repository\CalcResultsRepository;
use Langue\Log\Logger;
use Laminas\Log\PsrLoggerAdapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AuthorizationTokenFactory
{
    /**
     * @param ContainerInterface $container
     * @return AuthorizationTokenHandler
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AuthorizationTokenHandler
    {
        $calcResultsRepository = $container->get(CalcResultsRepository::class);
        $logger = $container->get(Logger::class);
        $logger->setChannel('AuthorizationTokenHandler');

        return new AuthorizationTokenHandler(
            $calcResultsRepository,
            new PsrLoggerAdapter($logger)
        );
    }
}
