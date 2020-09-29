<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\CalcResultsRepository;
use Laminas\Log\PsrLoggerAdapter;
use Langue\Log\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class CalcResultExtractingFactory
{
    /**
     * @param ContainerInterface $container
     * @return CalcResultExtractingService
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CalcResultExtractingService
    {
        $calcResultsRepository = $container->get(CalcResultsRepository::class);
        $logger = $container->get(Logger::class);
        $logger->setChannel('CalcResultExtractingService');

        return new CalcResultExtractingService(
            $calcResultsRepository,
            new PsrLoggerAdapter($logger)
        );
    }
}
