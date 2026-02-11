<?php

namespace Application\Controller\Stage\Factory;

use Application\Controller\Stage\SessionStageController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Misc\ImportForm;
use Application\Form\Stages\PeriodeStageForm;
use Application\Form\Stages\SessionStageForm;
use Application\Form\Stages\SessionStageRechercheForm;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\Contact\ContactStageService;
use Application\Service\Stage\SessionStageService;
use Application\Service\Stage\StageService;
use Doctrine\ORM\EntityManager;
use Evenement\Service\MailAuto\MailAutoStageDebutValidation;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenCalendrier\Service\CalendrierType\CalendrierTypeService;
use UnicaenCalendrier\Service\Date\DateService;
use UnicaenCalendrier\Service\DateType\DateTypeService;

/**
 * Class SessionStageControllerFactory
 * @package Application\Controller\Factory
 */
class SessionStageControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SessionStageController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SessionStageController
    {
        $controller = new SessionStageController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var SessionStageService $sessionStageService */
        $sessionStageService = $container->get(ServiceManager::class)->get(SessionStageService::class);
        $controller->setSessionStageService($sessionStageService);

        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $controller->setStageService($stageService);

        /**
         * @var SessionStageForm $sessionStageForm
         */
        $sessionStageForm = $container->get(FormElementManager::class)->get(SessionStageForm::class);
        $controller->setSessionStageForm($sessionStageForm);

        /**
         * @var SessionStageRechercheForm $sessionStageRechercheForm
         */
        $sessionStageRechercheForm = $container->get(FormElementManager::class)->get(SessionStageRechercheForm::class);
        $controller->setSessionStageRechercheForm($sessionStageRechercheForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        /** @var ImportForm $importFom */
        $importFom = $container->get(FormElementManager::class)->get(ImportForm::class);
        $controller->setImportForm($importFom);

        /** @var AnneeUniversitaireService $anneeService */
        $anneeService = $container->get(ServiceManager::class)->get(AnneeUniversitaireService::class);
        $controller->setAnneeUniversitaireService($anneeService);

        /** @var PeriodeStageForm $periodeStageForm */
        $periodeStageForm = $container->get(FormElementManager::class)->get(PeriodeStageForm::class);
        $controller->setPeriodeStageForm($periodeStageForm);
        /** @var CalendrierTypeService $service */
        $service = $container->get(ServiceManager::class)->get(CalendrierTypeService::class);
        $controller->setCalendrierTypeService($service);
        /** @var DateTypeService $service */
        $service = $container->get(ServiceManager::class)->get(DateTypeService::class);
        $controller->setDateTypeService($service);
        /** @var DateService $service */
        $service = $container->get(ServiceManager::class)->get(DateService::class);
        $controller->setDateService($service);

        return $controller;
    }
}