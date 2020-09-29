<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\CalcResultsRepository;
use Langue\Log\Logger;
use Laminas\Log\PsrLoggerAdapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PreserveNumberOfResultsFactory
{
    /**
     * @param ContainerInterface $container
     * @return PreserveNumberOfResultsService
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PreserveNumberOfResultsService
    {
        $calcResultsRepository = $container->get(CalcResultsRepository::class);
        $logger = $container->get(Logger::class);
        $logger->setChannel('PreserveNumberOfResultsService');

        return new PreserveNumberOfResultsService(
            $calcResultsRepository,
            new PsrLoggerAdapter($logger)
        );
    }
}
