<?php

namespace Application\Service\Referentiel\Factory;
use Application\Service\Referentiel\ReferentielPromoService;
use Doctrine\ORM\EntityManager;
use Interop\Container\Containerinterface;

class ReferentielPromoServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @return \Application\Service\Referentiel\ReferentielPromoService
     */
    public function __invoke(ContainerInterface $container) : ReferentielPromoService
    {
        $service = new ReferentielPromoService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        return $service;
    }


}