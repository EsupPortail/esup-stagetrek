<?php


namespace Application\Form\Parametre\Factory;

use Application\Form\Parametre\Hydrator\NiveauEtudeHydrator;
use Application\Form\Parametre\NiveauEtudeForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

/**
 * Class NiveauEtudeFormFactory
 * @package Application\Form\Factory
 */
class NiveauEtudeFormFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return NiveauEtudeForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NiveauEtudeForm
    {
        $form = new NiveauEtudeForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var NiveauEtudeHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(NiveauEtudeHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }
}