<?php

namespace Application\Form\Notification\Factory;

use Application\Form\Notification\Hydrator\MessageInfoHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class MessageInfoHydratorFactory
 * @package Application\Form\Factory\Hydrator
 */
class MessageInfoHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return MessageInfoHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MessageInfoHydrator
    {
        $hydrator = new MessageInfoHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        return $hydrator;
    }

}