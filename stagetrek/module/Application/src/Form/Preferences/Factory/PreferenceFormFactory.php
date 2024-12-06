<?php


namespace Application\Form\Preferences\Factory;


use Application\Form\Preferences\Hydrator\PreferenceHydrator;
use Application\Form\Preferences\PreferenceForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class AffectationStageFormFactory
 * @package Application\Form\Factory
 */
class PreferenceFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PreferenceForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PreferenceForm
    {
        $form = new PreferenceForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var PreferenceHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(PreferenceHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}
