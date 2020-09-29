<?php declare(strict_types=1);

namespace App\Repository;

use Laminas\Db\Adapter\Adapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class CalcResultsFactory
{
    /**
     * @param ContainerInterface $container
     * @return CalcResultsRepository
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CalcResultsRepository
    {
        $database = $container->get(Adapter::class);

        return new CalcResultsRepository($database);
    }
}
