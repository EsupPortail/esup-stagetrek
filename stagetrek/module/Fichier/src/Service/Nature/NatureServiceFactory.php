<?php

namespace Fichier\Service\Nature;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class NatureServiceFactory {

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): NatureService
    {
        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $service = new NatureService();
        $service->setObjectManager($entityManager);
        return $service;
    }
}