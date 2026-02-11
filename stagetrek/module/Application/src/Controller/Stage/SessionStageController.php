<?php

namespace Application\Controller\Stage;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\SessionStage;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Form\Misc\Traits\ImportFormAwareTrait;
use Application\Form\Stages\Traits\PeriodeStageFormAwareTrait;
use Application\Form\Stages\Traits\SessionStageFormAwareTrait;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;
use UnicaenCalendrier\Entity\Db\Calendrier;
use UnicaenCalendrier\Entity\Db\CalendrierType;
use UnicaenCalendrier\Entity\Db\Date;
use UnicaenCalendrier\Entity\Db\DateType;
use UnicaenCalendrier\Service\CalendrierType\CalendrierTypeServiceAwareTrait;
use UnicaenCalendrier\Service\Date\DateServiceAwareTrait;
use UnicaenCalendrier\Service\DateType\DateTypeServiceAwareTrait;

/**
 * Class SessionStageController
 * @package Application\Controller\Stages
 *
 * @method FlashMessenger flashMessenger()
 */
class SessionStageController extends AbstractActionController
{
    /** Accés aux formulaires */
    const ROUTE_INDEX = "session";

    const ROUTE_AFFICHER = "session/afficher";
    const ROUTE_AFFICHER_INFOS = "session/afficher-infos";
    const ROUTE_AFFICHER_DATES = "session/afficher-dates";
    const ROUTE_AJOUTER = "session/ajouter";
    const ROUTE_MODIFIER = "session/modifier";
    const ROUTE_SUPPRIMER = "session/supprimer";
    const ROUTE_AJOUTER_PERIODE = "session/periode-stage/ajouter";
    const ROUTE_MODIFIER_PERIODE = "session/periode-stage/modifier";
    const ROUTE_SUPPRIMER_PERIODE = "session/periode-stage/supprimer";
    const ROUTE_MODIFIER_PLACES_TERRAIN = "session/terrains/modifier";
    const ROUTE_IMPORTER_PLACES_TERRAIN = "session/terrains/importer";
    const ROUTE_MODIFIER_ORDRES_AFFECTAIONS = "session/stages/modifier-ordres";
    const ROUTE_RECALCULER_ORDRES_AFFECTAIONS = "session/stages/recalculer-ordres";

    const ACTION_INDEX = "index";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AFFICHER_INFOS = "afficher-infos";
    const ACTION_AFFICHER_DATES = "afficher-dates";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_AJOUTER_PERIODE_STAGE = "ajouter-periode-stage";
    const ACTION_MODIFIER_PERIODE_STAGE = "modifier-periode-stage";
    const ACTION_SUPPRIMER_PERIODE_STAGE = "supprimer-periode-stage";
    const ACTION_MODIFIER_PLACES_TERRAINS = "modifier-places-terrains";
    const ACTION_IMPORTER_PLACES_TERRAINS = "importer-places-terrains";
    const ACTION_MODIFIER_ORDRES_AFFECTATIONS = "modifier-ordres-affectations";
    const ACTION_RECALCULER_ORDRES_AFFECTATIONS = "recalculer-ordres-affectations";

    /** Events */
    const EVENT_AJOUTER = "event-ajouter-session-stage";
    const EVENT_MODIFIER = "event-modifier-session-stage";
    const EVENT_SUPPRIMER = "event-supprimer-session-stage";

    /** Accés aux entités */
    use AnneeUniversitaireServiceAwareTrait;
    use SessionStageServiceAwareTrait;
    use StageServiceAwareTrait;
    use SessionStageFormAwareTrait;
    use ConfirmationFormAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\DBAL\Exception
     */
    public function indexAction(): ViewModel
    {
        $form = $this->getSessionStageRechercheForm();
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            $criteria = [];
            if ($form->isValid()) {
                $criteria = array_filter($data, function ($v) {
                    return !empty($v);
                });
            }
            if (!empty($criteria)) {
                $sessionsStages = $this->getSessionStageService()->search($criteria);
            } else {
                $sessionsStages = $this->getSessionStageService()->findAll();
            }
        } else {
            $sessionsStages = $this->getSessionStageService()->findAll();
        }
        return new ViewModel(['form' => $form, 'sessionsStages' => $sessionsStages]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction(): ViewModel
    {
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        return new ViewModel(['sessionStage' => $session]);
    }


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function ajouterAction(): ViewModel
    {
        $title = "Ajouter une session de stage";
        $annee = $this->getAnneeUniversitaireFromRoute();
        $session = new SessionStage();
        $session->setAnneeUniversitaire($annee);
        $form = $this->getAddSessionStageForm();
        $form->bind($session);
        // Si l'année est précisé depuis la route on la prend
        if ($annee) {
            $form->setAnneeUniversitaire($annee);
        }
        if (isset($groupe)) {
            $form->setGroupe($groupe);
        }
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var SessionStage $session */
                    $session = $form->getData();
                    $this->getSessionStageService()->add($session);
                    $msg = sprintf("La session de stage %s a été créée.", $session->getLibelle());
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction(): ViewModel
    {
        $title = "Modifier la session de stage";
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        $sessionService = $this->getSessionStageService();
        $form = $this->getEditSessionStageForm();
        $form->setGroupe($session->getGroupe());
        $form->bind($session);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var SessionStage $session */
                    $session = $form->getData();
                    $sessionService->update($session);
                    $msg = sprintf("La session de stage %s a été modifiée.", $session->getLibelle());
                    $this->sendSuccessMessage($msg);
//                    return $this->successAction($title, $msg);
                    $form->bind($session);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierPlacesTerrainsAction(): ViewModel
    {
        $title = "Modifier les places disponibles";
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        if ($data = $this->params()->fromPost()) {
            if (isset($data['places'])) {
                foreach ($data['places'] as $terrainId => $nbPlace) {
                    $terrainId = intval($terrainId);
                    $nbPlace = intval($nbPlace);
                    $terrain = $session->getTerrainStageWithId($terrainId);
                    if (isset($terrain)) {
                        $session->setNbPlacesOuvertes($terrain, $nbPlace);
                    }
                }
                try {
                    $this->getSessionStageService()->update($session);
                    $this->getSessionStageService()->recomputeDemandeTerrains($session);
                    $this->sendSuccessMessage("Le nombre de place(s) ouverte(s) pour la session de stage a été mis à jours");
                    //Rechargement des données
                    $this->getObjectManager()->clear();
                    $session = $this->getSessionStageFromRoute();
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'sessionStage' => $session]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierOrdresAffectationsAction(): ViewModel
    {
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        $title = "Modifier les ordres d'affectations";
        if (empty($session->getStagesPrincipaux())) {
            $msg = sprintf("La session de stage %s n'as pas de stage de définie", $session->getLibelle());
            return $this->failureAction($title, $msg);
        }
        $stages = $session->getStagesPrincipaux();
        if ($data = $this->params()->fromPost()) {
            if (isset($data['ordresAffectations'])) {
                $stagesUpdated = [];
                foreach ($stages as $stage) {
                    $ordre = ($data['ordresAffectations'][$stage->getId()]) ?? null;
                    if (isset($ordre)) {
                        $ordre = intval($ordre);
                        if ($ordre == 0) {
                            $ordre = null;
                        }
                        if ($stage->getOrdreAffectationManuel() != $ordre) {
                            $stage->setOrdreAffectationManuel($ordre);
                            $stagesUpdated[$stage->getId()] = $stage;
                        }
                    }
                }
                if (!empty($stagesUpdated)) {
                    try {
                        foreach ($stagesUpdated as $stage) {
                            $this->getStageService()->update($stage);
                        }
                        $this->getStageService()->updateOrdresAffectations($session);
                        $this->sendSuccessMessage("Les ordres d'affectations des stages ont été mis à jours");
                        //Rechargement des données requis pour prendre en compte les modifications
                        $this->getObjectManager()->clear();
                        $session = $this->getSessionStageFromRoute();
                        $stages = $session->getStagesPrincipaux();
                    } catch (Exception $e) {
                        return $this->failureAction($title, null, $e);
                    }
                }
            }
        }
        return new ViewModel(['title' => $title, 'session' => $session, 'stages' => $stages,]);
    }


    public function recalculerOrdresAffectationsAction(): ViewModel
    {
        $title = "Recaculer les ordres d'affectations";
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment recalculer les ordres d'affectations ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getSessionStageService()->recomputeOrdresAffectations($session);
                $msg = "Les ordres d'affectations ont été recalculées";
                $form->addMessage($msg, Messenger::SUCCESS);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form, 'session' => $session]);
    }

    use PeriodeStageFormAwareTrait;
    use CalendrierTypeServiceAwareTrait;
    use DateTypeServiceAwareTrait;
    use DateServiceAwareTrait;

    public function ajouterPeriodeStageAction(): ViewModel
    {
        $title = "Ajouter une période de stage";
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        $form = $this->getAddPeriodeStageForm($session);
        $periode = new Date();
        $form->bind($periode);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                /** @var Date $periode */
                $periode = $form->getData();
                //Ajout du type a la période
                if($periode->getType() == null){
                    $type = $this->getDateTypeService()->getObjectManager()->getRepository(DateType::class)->findOneBy(['code' => SessionStage::DATES_PERIODE_STAGES]);
                    $periode->setType($type);
                }
                $calendrier = $session->getCalendrier();
                //TODO : création du calendrier s'il n'existe pas (pour gerer des sessions existante avant la mise en place d'UnicaenCalendrier)
                if(!isset($calendrier)){
                    /** @var CalendrierType $ct */
                    $ct = $this->getCalendrierTypeService()->getObjectManager()->getRepository(CalendrierType::class)->findOneBy(['code' => SessionStage::CALENDRIER_TYPE]);
                    $calendrier = new Calendrier();
                    $calendrier->setCalendrierType($ct);
                    $calendrier->setLibelle($ct->getLibelle()." ".$session->getLibelle()." - ".$session->getAnneeUniversitaire()->getLibelle()." - ".$session->getGroupe()->getLibelle());
                    $session->setCalendrier($calendrier);
                }
                $session->addDate($periode);
                $this->getSessionStageService()->update($session);
                    $msg = "La période de stage a été ajoutée.";
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }

        return new ViewModel(['title' => $title, 'form'=>$form, 'session' => $session]);
    }

    public function modifierPeriodeStageAction(): ViewModel
    {

        $title = "Modifier la période de stage";
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        /** @var Date $periode */
        $periode = $this->getDateFromRoute();
        $form = $this->getEditPeriodetageForm($session);
        $form->bind($periode);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Date $session */
                    $periode = $form->getData();
                    $this->getDateService()->update($periode);
                    $msg = "La période de stage a été modifiée.";
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'session' => $session, 'periode' => $periode]);
    }

    public function supprimerPeriodeStageAction(): ViewModel
    {
        $title = "Supprimer la période de stage";
        /** @var Date $periode */
        $periode = $this->getDateFromRoute();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer cette période de stage ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getDateService()->delete($periode);
                $msg = "La période de stage a été supprimée";
                $form->addMessage($msg, Messenger::SUCCESS);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }


    use ImportFormAwareTrait;
    use ImportValidatorTrait;
    /** TODO : prendre en compte les capactité maximale si le terrain de stage n'autorise pas la surcapacité
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function importerPlacesTerrainsAction(): ViewModel
    {
        var_dump("TODO : a revoir complétement");
        return new ViewModel([]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction(): ViewModel
    {
        $title = "Supprimer la session de stage";
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        $sessionService = $this->getSessionStageService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer la session de stage %s ?",
            $session->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $sessionService->delete($session);
                $msg = sprintf("La session de stage %s a été supprimée.",
                    $session->getLibelle()
                );
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

}