<?php

namespace Application\Controller\Contrainte\Factory;

use Application\Controller\Contrainte\ContrainteCursusEtudiantController;
use Application\Form\Contrainte\ContrainteCursusEtudiantForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Application\Service\Etudiant\EtudiantService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ContrainteCursusEtudiantControllerFactory
 * @package Application\Controller\Factory
 */
class ContrainteCursusEtudiantControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusEtudiantController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusEtudiantController
    {
        $controller = new ContrainteCursusEtudiantController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $controller->setEtudiantService($etudiantService);

        /** @var ContrainteCursusEtudiantService $contrainteCursusEtudiantService */
        $contrainteCursusEtudiantService = $container->get(ServiceManager::class)->get(ContrainteCursusEtudiantService::class);
        $controller->setContrainteCursusEtudiantService($contrainteCursusEtudiantService);

        /** @var ContrainteCursusEtudiantForm $contrainteCursusEtudiantForm */
        $contrainteCursusEtudiantForm = $container->get(FormElementManager::class)->get(ContrainteCursusEtudiantForm::class);
        $controller->setContrainteCursusEtudiantForm($contrainteCursusEtudiantForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }

}