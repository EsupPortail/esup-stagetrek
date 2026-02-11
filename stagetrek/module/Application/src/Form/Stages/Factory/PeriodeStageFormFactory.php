<?php

namespace Application\Form\Stages\Factory;

use Application\Form\Stages\Hydrator\PeriodeStageHydrator;
use Application\Form\Stages\Hydrator\SessionStageHydrator;
use Application\Form\Stages\PeriodeStageForm;
use Application\Form\Stages\SessionStageForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class SessionStageFormFactory
 * @package Application\Form\SessionsStages\Factory
 */
class PeriodeStageFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PeriodeStageForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PeriodeStageForm
    {
        $form = new PeriodeStageForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var PeriodeStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(PeriodeStageHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }
}
