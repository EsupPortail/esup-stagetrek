<?php

namespace Application\Form\Affectation\Factory;

use Application\Form\Affectation\AffectationStageForm;
use Application\Form\Affectation\Hydrator\AffectationStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class AffectationStageFormFactory
 * @package Application\Form\AffectationStage\Factory
 */
class AffectationStageFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AffectationStageForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AffectationStageForm
    {
        $form = new AffectationStageForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var AffectationStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(AffectationStageHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}
