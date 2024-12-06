<?php


namespace Application\Controller\Parametre\Factory;

use Application\Controller\Parametre\ParametreCoutAffectationController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Parametre\ParametreCoutAffectationForm;
use Application\Service\Parametre\ParametreCoutAffectationService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

class ParametreCoutAffectationControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutAffectationController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutAffectationController
    {
        $controller = new ParametreCoutAffectationController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ParametreCoutAffectationService $parametreCoutAffectationService */
        $parametreCoutAffectationService = $container->get(ServiceManager::class)->get(ParametreCoutAffectationService::class);
        $controller->setParametreCoutAffectationService($parametreCoutAffectationService);

        /** @var ParametreCoutAffectationForm $parametreCoutAffectationForm */
        $parametreCoutAffectationForm = $container->get(FormElementManager::class)->get(ParametreCoutAffectationForm::class);
        $controller->setParametreCoutAffectationForm($parametreCoutAffectationForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }

}