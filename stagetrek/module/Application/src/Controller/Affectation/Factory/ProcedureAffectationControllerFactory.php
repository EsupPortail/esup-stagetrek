<?php

namespace Application\Controller\Affectation\Factory;

use Application\Controller\Affectation\ProcedureAffectationController;
use Application\Form\Affectation\ProcedureAffectationForm;
use Application\Service\Affectation\ProcedureAffectationService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class AffectationControllerFactory
 * @package Application\Controller\Factory
 */
class ProcedureAffectationControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\Controller\Affectation\ProcedureAffectationController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ProcedureAffectationController
    {
        $controller = new ProcedureAffectationController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ProcedureAffectationService $procedureAffectationService */
        $procedureAffectationService = $container->get(ServiceManager::class)->get(ProcedureAffectationService::class);
        $controller->setProcedureAffectationService($procedureAffectationService);

        /** @var ProcedureAffectationForm $procedureAffectationForm */
        $procedureAffectationForm = $container->get(FormElementManager::class)->get(ProcedureAffectationForm::class);
        $controller->setProcedureAffectationForm($procedureAffectationForm);

        return $controller;
    }
}