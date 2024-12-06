<?php


namespace Application\Form\TerrainStage\Factory;


use Application\Form\TerrainStage\Hydrator\TerrainStageHydrator;
use Application\Form\TerrainStage\TerrainStageForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class TerrainStageFormFactory
 * @package Application\Form\TerrainStage\Factory
 */
class TerrainStageFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return TerrainStageForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TerrainStageForm
    {
        $form = new TerrainStageForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var TerrainStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(TerrainStageHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}
