<?php

namespace Application\Controller\Etudiant\Factory;

use Application\Controller\Etudiant\DisponibiliteController;
use Application\Form\Etudiant\DisponibiliteForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\Etudiant\DisponibiliteService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class DisponibiliteControllerFactory
 * @package Application\Controller\Factory
 */
class DisponibiliteControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DisponibiliteController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DisponibiliteController
    {
        $controller = new DisponibiliteController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var DisponibiliteService $disponibiliteService */
        $disponibiliteService = $container->get(ServiceManager::class)->get(DisponibiliteService::class);
        $controller->setDisponibiliteService($disponibiliteService);

        /** @var DisponibiliteForm $disponibiliteForm */
        $disponibiliteForm = $container->get(FormElementManager::class)->get(DisponibiliteForm::class);
        $controller->setDisponibiliteForm($disponibiliteForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }

}