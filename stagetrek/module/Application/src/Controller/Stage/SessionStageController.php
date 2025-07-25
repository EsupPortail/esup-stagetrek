<?php

namespace Application\Controller\Stage;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\SessionStage;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Form\Misc\Traits\ImportFormAwareTrait;
use Application\Form\Stages\Traits\SessionStageFormAwareTrait;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

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
    const ROUTE_MODIFIER_PLACES_TERRAIN = "session/terrains/modifier";
    const ROUTE_IMPORTER_PLACES_TERRAIN = "session/terrains/importer";
    const ROUTE_MODIFIER_ORDRES_AFFECTAIONS = "session/stages/modifier-ordres";

    const ACTION_INDEX = "index";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AFFICHER_INFOS = "afficher-infos";
    const ACTION_AFFICHER_DATES = "afficher-dates";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_MODIFIER_PLACES_TERRAINS = "modifier-places-terrains";
    const ACTION_IMPORTER_PLACES_TERRAINS = "importer-places-terrains";
    const ACTION_MODIFIER_ORDRES_AFFECTATIONS = "modifier-ordres-affectations";

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
    public function indexAction() : ViewModel
    {
        $form = $this->getSessionStageRechercheForm();
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            $criteria = [];
            if($form->isValid()) {
                $criteria = array_filter($data, function ($v) {
                    return !empty($v);
                });
            }

            if(!empty($criteria)) {
                $sessionsStages = $this->getSessionStageService()->search($criteria);
            }
            else{
                $sessionsStages = $this->getSessionStageService()->findAll();
            }
        }
        else {
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
                    $msg = sprintf("La session de stage %s a été créée.",
                        $session->getLibelle()
                    );
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }

        return new ViewModel(['title' => $title, 'form'=>$form]);
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
                    $msg = sprintf("La session de stage %s a été modifiée.",  $session->getLibelle() );
                    $this->sendSuccessMessage($msg);
//                    return $this->successAction($title, $msg);
                    $form->bind($session);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
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
            if(isset($data['places'])) {
                foreach ($data['places'] as $terrainId => $nbPlace) {
                    $terrainId = intval($terrainId);
                    $nbPlace = intval($nbPlace);
                    $terrain = $session->getTerrainStageWithId($terrainId);
                    if(isset($terrain)) {
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

        return new ViewModel(['title' => $title, 'sessionStage'=>$session]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierOrdresAffectationsAction() : ViewModel
    {
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        $title = "Modifier les ordres d'affectations";
        if(empty($session->getStagesPrincipaux())){
            $msg =  sprintf("La session de stage %s n'as pas de stage de définie", $session->getLibelle());
            return $this->failureAction($title,$msg );
        }

        $stages = $session->getStagesPrincipaux();
        if ($data = $this->params()->fromPost()) {
            if(isset($data['ordresAffectations'])) {
                $stagesUpdated = [];
                foreach ($stages as $stage) {
                    $ordre = ($data['ordresAffectations'][$stage->getId()]) ?? null;
                    if (isset($ordre)) {
                        $ordre = intval($ordre);
                        if($ordre==0){$ordre=null;}
                        if ($stage->getOrdreAffectationManuel() != $ordre) {
                            $stage->setOrdreAffectationManuel($ordre);
                            $stagesUpdated[$stage->getId()] = $stage;
                        }
                    }
                }
                if(!empty($stagesUpdated)) {
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

        return new ViewModel([
            'title' => $title,
            'session' => $session,
            'stages' => $stages,
        ]);
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