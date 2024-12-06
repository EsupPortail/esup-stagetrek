<?php

namespace Evenement\Service\Evenement\Factory;

use Doctrine\ORM\EntityManager;
use Evenement\Service\Evenement\EvenementService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use UnicaenEvenement\Service\Type\TypeService;

class EvenementServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return EvenementService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(Containerinterface $container, $requestedName, array $options = null)
    {
        /**
         * @var EntityManager $entityManager
         * @var TypeService $typeService
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $typeService = $container->get(TypeService::class);

        $service = new EvenementService();
        $service->setEntityManager($entityManager);
        $service->setObjectManager($entityManager);
        $service->setTypeService($typeService);

        return $service;
    }
}