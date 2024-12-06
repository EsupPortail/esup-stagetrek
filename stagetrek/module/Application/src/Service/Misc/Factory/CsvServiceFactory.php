<?php

namespace Application\Service\Misc\Factory;

use Application\Service\Misc\CSVService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class CsvServiceFactory
 * @package Application\Service\AlgorithmeAffectation\Factory
 */
class CsvServiceFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CSVService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CSVService
    {
        return new CSVService();
    }
}
