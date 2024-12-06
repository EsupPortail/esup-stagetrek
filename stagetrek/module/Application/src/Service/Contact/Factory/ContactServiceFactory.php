<?php

namespace Application\Service\Contact\Factory;

use Application\Service\Contact\ContactService;
use Application\Service\Misc\CSVService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

class ContactServiceFactory
{

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ContactService
    {
        $service = new ContactService();

        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);
        /** @var CSVService $csvService */
        $csvService = $container->get(ServiceManager::class)->get(CSVService::class);
        $service->setCsvService($csvService);

        return $service;
    }
}
