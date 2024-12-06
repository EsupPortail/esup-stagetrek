<?php


namespace Application\Form\Etudiant\Factory;

use Application\Form\Etudiant\EtudiantRechercheForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class EtudiantRechercheFormFactory
 * @package Application\Form\Factory
 */
class EtudiantRechercheFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EtudiantRechercheForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EtudiantRechercheForm
    {
        $form = new EtudiantRechercheForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);
//
//        /** @var EtudiantHydrator $hydrator */
//        $hydrator = $container->get('HydratorManager')->get(EtudiantHydrator::class);
//        $form->setHydrator($hydrator);

        return $form;
    }
}