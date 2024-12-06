<?php

namespace Application\Service\Notification\Factory;
use Application\Service\Notification\FaqCategorieQuestionService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class FaqCategorieQuestionServiceFactory
 * @package Application\Service\FAQ
 */
class FaqCategorieQuestionServiceFactory implements FactoryInterface
{

    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return \Application\Service\Notification\FaqCategorieQuestionService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FaqCategorieQuestionService
    {
        $service = new FaqCategorieQuestionService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        return $service;
    }
}
