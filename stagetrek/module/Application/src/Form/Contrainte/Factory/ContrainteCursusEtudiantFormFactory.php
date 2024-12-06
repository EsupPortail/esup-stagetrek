<?php

namespace Application\Form\Contrainte\Factory;

use Application\Form\Contrainte\ContrainteCursusEtudiantForm;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class ContrainteCursusEtudiantFormFactory
 * @package Application\Form\ContraintesCursus\Factory
 */
class ContrainteCursusEtudiantFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusEtudiantForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusEtudiantForm
    {
        $form = new ContrainteCursusEtudiantForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $form->setHydrator($hydrator);
        return $form;
    }

}
