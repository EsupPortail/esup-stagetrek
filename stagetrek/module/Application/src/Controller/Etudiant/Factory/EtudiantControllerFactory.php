<?php

namespace Application\Controller\Etudiant\Factory;

use Application\Controller\Etudiant\EtudiantController;
use Application\Form\Etudiant\EtudiantForm;
use Application\Form\Etudiant\EtudiantRechercheForm;
use Application\Form\Etudiant\ImportEtudiantForm;
use Application\Form\Misc\ConfirmationForm;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\Contrainte\ContrainteCursusService;
use Application\Service\Etudiant\EtudiantImportService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Groupe\GroupeService;
use Application\Service\Referentiel\ReferentielPromoService;
use Application\Service\Referentiel\ReferentielService;
use Application\Service\Stage\SessionStageService;
use Application\Service\Stage\StageService;
use Application\Validator\Import\EtudiantCsvImportValidator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class EtudiantControllerFactory
 * @package Application\Controller\Factory
 */
class EtudiantControllerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EtudiantController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EtudiantController
    {
        $controller = new EtudiantController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        $affectationStageService = $container->get(AffectationStageService::class);
        $controller->setAffectationStageService($affectationStageService);

        $contrainteCursusService = $container->get(ContrainteCursusService::class);
        $controller->setContrainteCursusService($contrainteCursusService);

        $etudiantService = $container->get(EtudiantService::class);
        $controller->setEtudiantService($etudiantService);

        $etudiantImportService = $container->get(EtudiantImportService::class);
        $controller->setEtudiantImportService($etudiantImportService);

        /** @var AnneeUniversitaireService $anneeService */
        $anneeService = $container->get(ServiceManager::class)->get(AnneeUniversitaireService::class);
        $controller->setAnneeUniversitaireService($anneeService);

        $groupeService = $container->get(GroupeService::class);
        $controller->setGroupeService($groupeService);

        $sessionStageService = $container->get(SessionStageService::class);
        $controller->setSessionStageService($sessionStageService);

        $stageService = $container->get(StageService::class);
        $controller->setStageService($stageService);

        $referentielService = $container->get(ReferentielService::class);
        $controller->setReferentielService($referentielService);

        $referentielPromoService = $container->get(ReferentielPromoService::class);
        $controller->setReferentielPromoService($referentielPromoService);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        $etudiantForm = $container->get(FormElementManager::class)->get(EtudiantForm::class);
        $controller->setEtudiantForm($etudiantForm);

        $etudiantRechercheForm = $container->get(FormElementManager::class)->get(EtudiantRechercheForm::class);
        $controller->setEtudiantRechercheForm($etudiantRechercheForm);

        $importEtudiantForm = $container->get(FormElementManager::class)->get(ImportEtudiantForm::class);
        $controller->setImportEtudiantForm($importEtudiantForm);
        $etudiantCsvImportValidator =  $container->get(ValidatorPluginManager::class)->get(EtudiantCsvImportValidator::class);
        $controller->setImportValidator($etudiantCsvImportValidator);

        return $controller;
    }

}