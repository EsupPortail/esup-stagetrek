<?php


namespace Application\Form\Groupe\Factory;

use Application\Form\Groupe\GroupeForm;
use Application\Form\Groupe\Hydrator\GroupeHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class GroupeFormFactory
 * @package Application\Form\Groupe\Factory
 */
class GroupeFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GroupeForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GroupeForm
    {
        $form = new GroupeForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var GroupeHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(GroupeHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}