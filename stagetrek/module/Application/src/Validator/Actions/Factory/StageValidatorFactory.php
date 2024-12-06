<?php

namespace Application\Validator\Actions\Factory;

use Application\Validator\Actions\StageValidator;
use Application\Validator\Actions\ValidationStageValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

class StageValidatorFactory implements AbstractFactoryInterface
{

    /**
     * Can the factory create an instance for the service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return class_exists($requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StageValidator
    {
        return new StageValidator();
    }

}