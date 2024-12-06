<?php


namespace Application\Form\Etudiant\Factory;

use Application\Form\Etudiant\EtudiantForm;
use Application\Form\Etudiant\Hydrator\EtudiantHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class EtudiantFormFactory
 * @package Application\Form\Factory
 */
class EtudiantFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EtudiantForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EtudiantForm
    {
        $form = new EtudiantForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var EtudiantHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(EtudiantHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }
}