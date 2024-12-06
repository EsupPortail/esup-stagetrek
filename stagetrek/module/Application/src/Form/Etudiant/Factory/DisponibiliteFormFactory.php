<?php


namespace Application\Form\Etudiant\Factory;

use Application\Form\Etudiant\DisponibiliteForm;
use Application\Form\Etudiant\Hydrator\DisponibiliteHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class DisponibiliteFormFactory
 * @package Application\Form\Disponibilite
 */
class DisponibiliteFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DisponibiliteForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DisponibiliteForm
    {
        $form = new DisponibiliteForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var DisponibiliteHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DisponibiliteHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}
