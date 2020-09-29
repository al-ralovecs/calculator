<?php declare(strict_types=1);

namespace App\Handler;

use App\Service\PreserveNumberOfResultsService;
use Laminas\Log\PsrLoggerAdapter;
use Langue\Log\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class StoreCalcResultFactory
{
    /**
     * @param ContainerInterface $container
     * @return StoreCalcResultHandler
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): StoreCalcResultHandler
    {
        $preserveNumberOfResultsService = $container->get(PreserveNumberOfResultsService::class);
        $logger = $container->get(Logger::class);
        $logger->setChannel('StoreCalcResultHandler');

        return new StoreCalcResultHandler(
            $preserveNumberOfResultsService,
            new PsrLoggerAdapter($logger)
        );
    }
}
