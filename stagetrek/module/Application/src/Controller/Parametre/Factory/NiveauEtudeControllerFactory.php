<?php


namespace Application\Controller\Parametre\Factory;

use Application\Controller\Parametre\NiveauEtudeController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Parametre\NiveauEtudeForm;
use Application\Service\Parametre\NiveauEtudeService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class NiveauEtudeControllerFactory
 * @package Application\Controller\Etudiants\Factory
 */
class NiveauEtudeControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return NiveauEtudeController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NiveauEtudeController
    {
        $controller = new NiveauEtudeController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        $niveauEtudeService = $container->get(ServiceManager::class)->get(NiveauEtudeService::class);
        $controller->setNiveauEtudeService($niveauEtudeService);

        $niveauEtudeForm = $container->get(FormElementManager::class)->get(NiveauEtudeForm::class);
        $controller->setNiveauEtudeForm($niveauEtudeForm);

        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }

}