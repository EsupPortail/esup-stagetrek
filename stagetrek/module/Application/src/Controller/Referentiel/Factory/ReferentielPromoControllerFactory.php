<?php

namespace Application\Controller\Referentiel\Factory;

use Application\Controller\Referentiel\ReferentielPromoController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Referentiel\ReferentielPromoForm;
use Application\Service\Referentiel\ReferentielPromoService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ReferentielControllerFactory
 * @package Referentiel\Controller
 */
class ReferentielPromoControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ReferentielPromoController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ReferentielPromoController
    {
        $controller = new ReferentielPromoController();


        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var ReferentielPromoService $referentielPromoService */
        $referentielPromoService = $container->get(ReferentielPromoService::class);
        $controller->setReferentielPromoService($referentielPromoService);

        /** @var ReferentielPromoForm $referentielPromoForm */
        $referentielPromoForm = $container->get(FormElementManager::class)->get(ReferentielPromoForm::class);
        $controller->setReferentielPromoForm($referentielPromoForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }
}