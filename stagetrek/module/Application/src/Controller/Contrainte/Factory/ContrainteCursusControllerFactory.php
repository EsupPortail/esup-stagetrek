<?php

namespace Application\Controller\Contrainte\Factory;

use Application\Controller\Contrainte\ContrainteCursusController;
use Application\Form\Contrainte\ContrainteCursusForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\Contrainte\ContrainteCursusService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ContrainteCursusControllerFactory
 * @package Application\Controller\Factory
 */
class ContrainteCursusControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusController
    {
        $controller = new ContrainteCursusController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        /** @var ContrainteCursusService $service */
        $service = $container->get(ServiceManager::class)->get(ContrainteCursusService::class);
        $controller->setContrainteCursusService($service);

        /** @var ContrainteCursusForm $form */
        $form = $container->get(FormElementManager::class)->get(ContrainteCursusForm::class);
        $controller->setContrainteCursusForm($form);

        return $controller;
    }

}