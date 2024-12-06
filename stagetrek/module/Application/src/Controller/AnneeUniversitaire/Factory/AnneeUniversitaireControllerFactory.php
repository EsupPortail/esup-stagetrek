<?php

namespace Application\Controller\AnneeUniversitaire\Factory;

use Application\Controller\AnneeUniversitaire\AnneeUniversitaireController;
use Application\Form\Annees\AnneeUniversitaireForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\Groupe\GroupeService;
use Application\Service\Stage\SessionStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class AnneeUniversitaireControllerFactory
 * @package Application\Controller\Factory
 */
class AnneeUniversitaireControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AnneeUniversitaireController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnneeUniversitaireController
    {
        $controller = new AnneeUniversitaireController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var AnneeUniversitaireService $anneeUniversitaireService */
        $anneeUniversitaireService = $container->get(ServiceManager::class)->get(AnneeUniversitaireService::class);
        $controller->setAnneeUniversitaireService($anneeUniversitaireService);

        /** @var GroupeService $groupeService */
        $groupeService = $container->get(ServiceManager::class)->get(GroupeService::class);
        $controller->setGroupeService($groupeService);

        /** @var SessionStageService $sessionStageService */
        $sessionStageService = $container->get(ServiceManager::class)->get(SessionStageService::class);
        $controller->setSessionStageService($sessionStageService);

        /** @var AnneeUniversitaireForm $anneeUniversitaireForm */
        $anneeUniversitaireForm = $container->get(FormElementManager::class)->get(AnneeUniversitaireForm::class);
        $controller->setAnneeUniversitaireForm($anneeUniversitaireForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);


        return $controller;
    }
}