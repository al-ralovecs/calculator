<?php declare(strict_types=1);

namespace App;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterServiceFactory;
use Tuupola\Middleware\CorsMiddleware;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
            ],
            'factories' => [
                // third party
                Adapter::class => AdapterServiceFactory::class,
                CorsMiddleware::class => Middleware\CorsMiddlewareFactory::class,
                // Handler
                Handler\AuthorizationTokenHandler::class => Handler\AuthorizationTokenFactory::class,
                Handler\ObtainCalcResultHandler::class => Handler\ObtainCalcResultFactory::class,
                Handler\StoreCalcResultHandler::class => Handler\StoreCalcResultFactory::class,
                // Service
                Service\PreserveNumberOfResultsService::class => Service\PreserveNumberOfResultsFactory::class,
                Service\CalcResultExtractingService::class => Service\CalcResultExtractingFactory::class,
                // Repository
                Repository\CalcResultsRepository::class => Repository\CalcResultsFactory::class,
            ],
        ];
    }
}
