<?php declare(strict_types=1);

namespace App\Handler;

use App\Service\CalcResultExtractingService;
use Laminas\Log\PsrLoggerAdapter;
use Langue\Log\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ObtainCalcResultFactory
{
    /**
     * @param ContainerInterface $container
     * @return ObtainCalcResultHandler
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ObtainCalcResultHandler
    {
        $calcResultExtractingService = $container->get(CalcResultExtractingService::class);
        $logger = $container->get(Logger::class);
        $logger->setChannel('ObtainCalcResultHandler');

        return new ObtainCalcResultHandler(
            $calcResultExtractingService,
            new PsrLoggerAdapter($logger)
        );
    }
}
