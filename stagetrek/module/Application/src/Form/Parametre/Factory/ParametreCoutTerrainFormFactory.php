<?php


namespace Application\Form\Parametre\Factory;

use Application\Form\Parametre\Hydrator\ParametreCoutTerrainHydrator;
use Application\Form\Parametre\ParametreCoutTerrainForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class ParametreTerrainCoutAffectationFixeFormFactory
 * @package Application\Form\Factory
 */
class ParametreCoutTerrainFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutTerrainForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutTerrainForm
    {
        $form = new ParametreCoutTerrainForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var ParametreCoutTerrainHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ParametreCoutTerrainHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}
