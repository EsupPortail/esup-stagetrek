<?php


namespace Application\Controller\Parametre\Factory;

use Application\Controller\Parametre\ParametreCoutTerrainController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Parametre\ParametreCoutTerrainForm;
use Application\Service\Parametre\ParametreCoutTerrainService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ParametreCoutTerrainController
 * @package Application\Controller\Etudiants\Factory
 */
class ParametreCoutTerrainControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutTerrainController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutTerrainController
    {
        $controller = new ParametreCoutTerrainController();


        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ParametreCoutTerrainService $parametreCoutTerrainService */
        $parametreCoutTerrainService = $container->get(ServiceManager::class)->get(ParametreCoutTerrainService::class);
        $controller->setParametreCoutTerrainService($parametreCoutTerrainService);

        /** @var ParametreCoutTerrainForm $parametreCoutTerrainForm */
        $parametreCoutTerrainForm = $container->get(FormElementManager::class)->get(ParametreCoutTerrainForm::class);
        $controller->setParametreCoutTerrainForm($parametreCoutTerrainForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }

}