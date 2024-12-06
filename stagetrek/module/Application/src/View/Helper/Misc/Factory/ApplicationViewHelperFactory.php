<?php

namespace Application\View\Helper\Misc\Factory;

use Application\View\Helper\Misc\ApplicationViewHelper;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class AlertFlashViewHelperFactory
 * @package Application\View\Helper\Messages\Factory
 */
class ApplicationViewHelperFactory implements FactoryInterface
{
    /**
     * Create view helper
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ApplicationViewHelper
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ApplicationViewHelper
    {
        $vh = new ApplicationViewHelper();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $vh->setObjectManager($entityManager);

        return $vh;
    }
}