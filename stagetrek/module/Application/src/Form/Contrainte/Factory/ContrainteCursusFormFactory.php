<?php

namespace Application\Form\Contrainte\Factory;

use Application\Form\Contrainte\ContrainteCursusForm;
use Application\Form\Contrainte\Hydrator\ContrainteCursusHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class ContrainteCursusFormFactory
 * @package Application\Form\ContraintesCursus\Factory
 */
class ContrainteCursusFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusForm
    {
        $form = new ContrainteCursusForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var ContrainteCursusHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ContrainteCursusHydrator::class);
        $form->setHydrator($hydrator);
        return $form;
    }

}
