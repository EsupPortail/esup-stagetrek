<?php


namespace Application\Controller\Parametre\Factory;

use Application\Controller\Parametre\ParametreController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Parametre\ParametreForm;
use Application\Service\Parametre\ParametreService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ParametreControllerFactory
 * @package Application\Controller\Etudiants\Factory
 */
class ParametreControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreController
    {
        $controller = new ParametreController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ParametreService  $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $controller->setParametreService($parametreService);

        /** @var ParametreForm $parametreForm */
        $parametreForm = $container->get(FormElementManager::class)->get(ParametreForm::class);
        $controller->setParametreForm($parametreForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }

}