<?php

namespace Application\Form\Stages\Factory;

use Application\Form\Stages\Hydrator\SessionStageHydrator;
use Application\Form\Stages\SessionStageForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class SessionStageFormFactory
 * @package Application\Form\SessionsStages\Factory
 */
class SessionStageFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SessionStageForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SessionStageForm
    {
        $form = new SessionStageForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var SessionStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(SessionStageHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }
}
