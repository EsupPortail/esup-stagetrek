<?php

namespace Application\Service\Referentiel\Factory;
use Application\Service\Referentiel\SourceService;
use Doctrine\ORM\EntityManager;
use Interop\Container\Containerinterface;

class SourceServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @return \Application\Service\Referentiel\SourceService
     */
    public function __invoke(ContainerInterface $container) : SourceService
    {
        $service = new SourceService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        return $service;
    }

}