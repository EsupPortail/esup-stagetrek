<?php

namespace Evenement\Service\Evenement\Factory;

use Application\Service\Mail\MailService;
use Evenement\Service\Evenement\EvenementTestService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenEvenement\Service\Etat\EtatService;
use UnicaenEvenement\Service\Evenement\EvenementService;
use UnicaenEvenement\Service\Type\TypeService;

class EvenementTestServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return \Evenement\Service\Evenement\EvenementTestService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(Containerinterface $container, $requestedName, array $options = null) : EvenementTestService
    {

        $service = new EvenementTestService();
        $service->setTypeService($container->get(TypeService::class));
        $service->setEtatEvenementService($container->get(EtatService::class));
        $service->setEvenementService($container->get(EvenementService::class));
        $service->setMailService($container->get(MailService::class));

        return $service;
    }
}