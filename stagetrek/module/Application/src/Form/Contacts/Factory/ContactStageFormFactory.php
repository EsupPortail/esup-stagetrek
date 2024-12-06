<?php


namespace Application\Form\Contacts\Factory;


use Application\Form\Contacts\ContactStageForm;
use Application\Form\Contacts\Hydrator\ContactStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

class ContactStageFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContactStageForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContactStageForm
    {
        $form = new ContactStageForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var ContactStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ContactStageHydrator::class);
        $form->setHydrator($hydrator);
        return $form;
    }

}
