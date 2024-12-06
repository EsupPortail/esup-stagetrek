<?php

namespace Application\Service\Contact\Factory;

use Application\Service\Contact\ContactTerrainService;
use Application\Service\Misc\CSVService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

class ContactTerrainServiceFactory
{

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ContactTerrainService
    {
        $serviceProvider = new ContactTerrainService();
        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var CSVService $csvService */
        $csvService = $container->get(ServiceManager::class)->get(CSVService::class);
        $serviceProvider->setCsvService($csvService);
        return $serviceProvider;
    }
}
