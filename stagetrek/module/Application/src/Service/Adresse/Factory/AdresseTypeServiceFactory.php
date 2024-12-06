<?php

namespace Application\Service\Adresse\Factory;

use Application\Service\Adresse\AdresseTypeService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AdresseTypeServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AdresseTypeService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AdresseTypeService
    {
        $sercice = new AdresseTypeService();
        $entityManager = $container->get(EntityManager::class);
        $sercice->setObjectManager($entityManager);


        return $sercice;
    }
}
