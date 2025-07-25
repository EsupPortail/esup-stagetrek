<?php

namespace Application\Controller\Index;

use Application\Service\Notification\MessageInfoService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class IndexControllerFactory
 * @package Application\Controller\Factory
 */
class IndexControllerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return IndexController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IndexController
    {
//        $config = $container->get('Config');
        $controller = new IndexController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        $messageInfoService = $container->get(ServiceManager::class)->get(MessageInfoService::class);
        $controller->setMessageInfoService($messageInfoService);
        return $controller;
    }

}