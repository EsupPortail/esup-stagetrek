<?php


namespace Application\Form\Contacts\Factory;


use Application\Form\Contacts\ContactTerrainForm;
use Application\Form\Contacts\Hydrator\ContactTerrainHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

class ContactTerrainFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactTerrainForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactTerrainForm
    {
        $form = new ContactTerrainForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var ContactTerrainHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ContactTerrainHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}
