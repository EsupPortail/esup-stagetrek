<?php

namespace Application\Validator\Import\Factory;

use Application\Service\Groupe\GroupeService;
use Application\Service\Misc\CSVService;
use Application\Validator\Import\EtudiantCsvImportValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceManager;

class EtudiantCsvImportValidatorFactory implements AbstractFactoryInterface
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
     * @param EtudiantCsvImportValidator $validator
     * @param ContainerInterface $container
     * @return EtudiantCsvImportValidator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function initValidator(EtudiantCsvImportValidator $validator, ContainerInterface $container): EtudiantCsvImportValidator
    {
//        TODO : a revoir, probablement utiliser l'abstractFactory car identique
        /**
         * @var ServiceManager $serviceManager
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $serviceManager = $container->get(ServiceManager::class);

        $validator->setObjectManager($entityManager);
        $validator->setServiceManager($serviceManager);
        $validator->setCsvService( $container->get(ServiceManager::class)->get(CSVService::class));

        $validator->setGroupeService($container->get(ServiceManager::class)->get(GroupeService::class));

        return $validator;
    }
}