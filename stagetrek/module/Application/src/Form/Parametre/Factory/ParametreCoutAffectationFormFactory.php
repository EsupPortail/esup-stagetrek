<?php


namespace Application\Form\Parametre\Factory;

use Application\Form\Parametre\ParametreCoutAffectationForm;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class ParametreCoutAffectationFormFactory
 * @package Application\Form\Factory
 */
class ParametreCoutAffectationFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutAffectationForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutAffectationForm
    {
        $form = new ParametreCoutAffectationForm();

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
