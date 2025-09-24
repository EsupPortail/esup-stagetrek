<?php

namespace Application\Form\Referentiel\Factory;

use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsForm;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\View\HelperPluginManager;

class ImportEtudiantsFormFactory implements AbstractFactoryInterface
{
    /**
     * Can the factory create an instance for the service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return class_exists($requestedName)
            && in_array(AbstractImportEtudiantsForm::class, class_implements($requestedName));
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AbstractImportEtudiantsForm
    {
        $form = new $requestedName;
        $this->initForm($form, $container);

        return $form;
    }

    /**
     * Initialize service
     *
     * @param AbstractImportEtudiantsForm $form
     * @param ContainerInterface $container
     * @return AbstractImportEtudiantsForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function initForm(AbstractImportEtudiantsForm $form, ContainerInterface $container): AbstractImportEtudiantsForm
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        return $form;
    }

}