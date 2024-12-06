<?php

namespace Application\Service\Contact\Factory;

use Application\Service\Contact\ContactStageService;
use Application\Service\Mail\MailService;
use Application\Service\Parametre\ParametreService;
use Application\Service\Stage\ValidationStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

class ContactStageServiceFactory
{

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ContactStageService
    {
        /** @var ContactStageService $service */
        $serviceProvider = new ContactStageService();

        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);


        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $serviceProvider->setParametreService($parametreService);

        /** @var ValidationStageService $validationService */
        $validationService = $container->get(ServiceManager::class)->get(ValidationStageService::class);
        $serviceProvider->setValidationStageService($validationService);


        /** @var MailService $mailService */
        $mailService = $container->get(ServiceManager::class)->get(MailService::class);
        $serviceProvider->setMailService($mailService);

        return $serviceProvider;
    }
}
