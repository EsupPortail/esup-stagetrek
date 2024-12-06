<?php

namespace Evenement\Service\MailAuto\Factory;

use Application\Service\Mail\MailService;
use Doctrine\ORM\EntityManager;
use Evenement\Service\MailAuto\Abstract\AbstractMailAutoEvenementService;
use Evenement\Service\MailAuto\Abstract\MailAutoEvenementServiceInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEvenement\Service\Etat\EtatService;
use UnicaenEvenement\Service\Evenement\EvenementService;
use UnicaenEvenement\Service\EvenementCollection\EvenementCollectionService;
use UnicaenEvenement\Service\Type\TypeService;

class MailAutoEvenementServiceFactory
{
    /**
     * Can the factory create an instance for the service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, string $requestedName) : bool
    {
        return class_exists($requestedName);
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : MailAutoEvenementServiceInterface
    {
        $service = new $requestedName;
        $this->initMailAutoEventService($service, $container);

        return $service;
    }

    /**
     * Initialize service
     *
     * @param AbstractMailAutoEvenementService $service
     * @param ContainerInterface $container
     * @return AbstractMailAutoEvenementService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function initMailAutoEventService(AbstractMailAutoEvenementService $service, ContainerInterface $container): AbstractMailAutoEvenementService
    {
        /**
         * @var EntityManager $entityManager
         * @var EtatService $etatService
         * @var EvenementService $evenementService
         * @var EvenementCollectionService $evenementCollectionService
         * @var TypeService $typeService
         * @var MailService $mailService
         * @var ServiceManager $serviceManager
         */
        $entityManager = $container->get(EntityManager::class);
        $etatService = $container->get(EtatService::class);
        $evenementService = $container->get(EvenementService::class);
        $typeService = $container->get(TypeService::class);
        $evenementCollectionService = $container->get(EvenementCollectionService::class);
        $mailService = $container->get(MailService::class);
        $serviceManager = $container->get(ServiceManager::class);

        $service->setObjectManager($entityManager);
        $service->setEtatEvenementService($etatService);
        $service->setTypeService($typeService);
        $service->setEvenementService($evenementService);
        $service->setEvenementCollectionService($evenementCollectionService);
        $service->setMailService($mailService);

        //Pour pouvoir en cas de besoins faire appel à d'autres service
        $service->setServiceManager($serviceManager);
        return $service;
    }
}