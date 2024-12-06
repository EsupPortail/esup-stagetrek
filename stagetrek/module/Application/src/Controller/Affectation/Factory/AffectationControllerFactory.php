<?php

namespace Application\Controller\Affectation\Factory;

use Application\Controller\Affectation\AffectationController;
use Application\Form\Affectation\AffectationStageForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Affectation\ProcedureAffectationService;
use Application\Service\Contact\ContactStageService;
use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Mail\MailService;
use Application\Service\Preference\PreferenceService;
use Application\Service\Stage\SessionStageService;
use Doctrine\ORM\EntityManager;
use Evenement\Service\MailAuto\MailAutoAffectationEvenementService;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class AffectationControllerFactory
 * @package Application\Controller\Factory
 */
class AffectationControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AffectationController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AffectationController
    {
        $controller = new AffectationController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var AffectationStageService $affectationStageService */
        $affectationStageService = $container->get(ServiceManager::class)->get(AffectationStageService::class);
        $controller->setAffectationStageService($affectationStageService);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $controller->setEtudiantService($etudiantService);

        /** @var PreferenceService $preferenceService */
        $preferenceService = $container->get(ServiceManager::class)->get(PreferenceService::class);
        $controller->setPreferenceService($preferenceService);

        /** @var SessionStageService $sessionStageService */
        $sessionStageService = $container->get(ServiceManager::class)->get(SessionStageService::class);
        $controller->setSessionStageService($sessionStageService);

        /** @var AffectationStageForm $affectationStageForm */
        $affectationStageForm = $container->get(FormElementManager::class)->get(AffectationStageForm::class);
        $controller->setAffectationStageForm($affectationStageForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        /** @var ContrainteCursusEtudiantService $contrainteCursusEtudiantService */
        $contrainteCursusEtudiantService = $container->get(ServiceManager::class)->get(ContrainteCursusEtudiantService::class);
        $controller->setContrainteCursusEtudiantService($contrainteCursusEtudiantService);

        /** @var MailService $mailService */
        $mailService = $container->get(ServiceManager::class)->get(MailService::class);
        $controller->setMailService($mailService);

        /** @var MailAutoAffectationEvenementService $mailAutoService */
        $mailAutoService = $container->get(ServiceManager::class)->get(MailAutoAffectationEvenementService::class);
        $controller->setMailAutoAffectationEvenementService($mailAutoService);

        /** @var ContactStageService $contactStageService */
        $contactStageService = $container->get(ServiceManager::class)->get(ContactStageService::class);
        $controller->setContactStageService($contactStageService);

        /** @var ProcedureAffectationService $procedureAffectationService */
        $procedureAffectationService = $container->get(ServiceManager::class)->get(ProcedureAffectationService::class);
        $controller->setProcedureAffectationService($procedureAffectationService);

        return $controller;
    }
}