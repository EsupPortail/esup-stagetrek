<?php

namespace Application\Service\Notification\Factory;

use Application\Service\Notification\MessageInfoService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Renderer\PhpRenderer;
use UnicaenAuthentification\Service\UserContext;
use UnicaenPrivilege\Service\AuthorizeService;

/**
 * Class MessageInfoServiceFactory
 * @package Application\Service\Messages
 */
class MessageInfoServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return MessageInfoService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MessageInfoService
    {
        $service = new MessageInfoService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        /** @var UserContext $userContext */
        $userContext = $container->get('UnicaenAuthentification\Service\UserContext');
        $service->setServiceUserContext($userContext);

        /** @var PhpRenderer $viewRenderer */
        $viewRenderer = $container->get('ViewRenderer');
        $service->setRenderer($viewRenderer);

        /** @var AuthorizeService $authorizeService */
        $authService = $container->get('BjyAuthorize\Service\Authorize');
        $service->setServiceAuthorize($authService);

        return $service;
    }
}
