<?php

namespace Application\Validator\Actions\Factory;

use Application\Validator\Actions\ValidationStageValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

class ValidationStageValidatorFactory implements AbstractFactoryInterface
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

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ValidationStageValidator
    {
        $validator = new ValidationStageValidator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $validator->setObjectManager($entityManager);

        return $validator;
    }

}