<?php

namespace Application\Validator\Import\Factory;

use Application\Service\Misc\CSVService;
use Application\Validator\Import\Interfaces\AbstractCsvImportValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceManager;

class AbstractImportCsvValidatorFactory implements AbstractFactoryInterface
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

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $validator = new $requestedName;
        $this->initValidator($validator, $container);
        return $validator;
    }

    /**
     * Initialize service
     *
     * @param AbstractCsvImportValidator $validator
     * @param ContainerInterface $container
     * @return AbstractCsvImportValidator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function initValidator(AbstractCsvImportValidator $validator, ContainerInterface $container): AbstractCsvImportValidator
    {
        /**
         * @var ServiceManager $serviceManager
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $serviceManager = $container->get(ServiceManager::class);
        $validator->setObjectManager($entityManager);
        $validator->setServiceManager($serviceManager);

        /** @var CSVService $csvService */
        $csvService = $container->get(ServiceManager::class)->get(CSVService::class);
        $validator->setCsvService($csvService);

        return $validator;
    }
}