<?php

namespace Application\Controller\Stage;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Application\Validator\Actions\Traits\StageValidatorAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

/**
 * Class SessionStageController
 * @package Application\Controller
 *
 * @method FlashMessenger flashMessenger()
 */
class StageController extends AbstractActionController
{
    const ROUTE_AFFICHER = "stage/afficher";
    const ROUTE_AFFICHER_INFOS = "stage/afficher-infos";
    const ROUTE_AFFICHER_AFFECTATION = "stage/afficher-affectation";
    const ROUTE_AFFICHER_CONVENTION = "stage/afficher-convention";
    const ROUTE_LISTER = "stage/lister";
    const ROUTE_AJOUTER_STAGES = "stage/ajouter";
    const ROUTE_SUPPRIMER_STAGES = "stage/supprimer";
    const ROUTE_LISTER_CONTACTS = "stage/lister-contacts";

    const ACTION_AFFICHER = "afficher";
    const ACTION_AFFICHER_INFOS = "afficher-infos";
    const ACTION_LISTER_CONTACTS = "lister-contacts";
    const ACTION_AFFICHER_AFFECTATION = "afficher-affectation";
    const ACTION_AFFICHER_CONVENTION = "afficher-convention";

    /** @desc pour les ajouts/modifications multiples via les sessions */
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER_STAGES = "ajouter-stages";
    const ACTION_MODIFIER_ORDRES_AFFECTATIONS = "modifier-ordres-affectations";
    const ACTION_SUPPRIMER_STAGES = "supprimer-stages";

    const EVENT_AJOUTER = "event-supprimer-stage";
    const EVENT_MODIFIER = "event-modifier-stage";
    const EVENT_SUPPRIMER = "event-supprimer-stage";


    use StageServiceAwareTrait;
    use StageValidatorAwareTrait;


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction() : ViewModel
    {
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }

    public function afficherInfosAction() : ViewModel
    {
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAffectationAction() : ViewModel
    {
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherConventionAction() : ViewModel
    { //TODO : a mettre dans ConventionController ?
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerStagesAction() : ViewModel
    {
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        $stages = $session->getStages()->toArray();
        return new ViewModel(['stages' => $stages]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerContactsAction() : ViewModel
    {
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function ajouterStagesAction() : ViewModel
    {
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();
        $title = sprintf("Ajouter des stages à la session %s", $session->getLibelle());
        $validator = $this->getStageValidator();

        if ($etudiants = $this->getEtudiantsFromPost()) {
            try {
//            Sécurité : on vérifie que l'on peut bien ajouté les étudiants fournis
                $etudiants = array_filter($etudiants, function (Etudiant $etudiant) use ($session, $validator) {
                    return $validator->assertAjouter($session, $etudiant);
                });
                    $stages = [];
                foreach ($etudiants as $etudiant){
                    $stages[] = $this->getStageService()->createStage($etudiant,$session);
                }
                $this->getStageService()->addMultiple($stages);
                $msg = "Les stages ont été créés";
                $this->sendSuccessMessage($msg, self::ACTION_AJOUTER_STAGES);
                $this->getObjectManager()->clear();
                $session = $this->getSessionStageFromRoute();
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }

        $etudiants = $session->getGroupe()->getEtudiants()->toArray();
        $etudiants = array_filter($etudiants, function (Etudiant $etudiant) use ($session, $validator) {
            return $validator->assertAjouter($session, $etudiant);
        });
        return new ViewModel([
            'title' => $title,
            'session' => $session,
            'etudiants' => $etudiants,

        ]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerStagesAction() : ViewModel
    {
        /** @var SessionStage $session */
        $session = $this->getSessionStageFromRoute();

        $title = sprintf("Supprimer des stages de la session %s", $session->getLibelle());
        $validator = $this->getStageValidator();

        if ($stages = $this->getStagesFromPost()) {
            try {
                $stages = array_filter($stages, function (Stage $stage) use ($session, $validator) {
                    return $validator->assertSupprimer($session, $stage);
                });
                $this->getStageService()->deleteMultiple($stages);
                $msg =  "Les stages ont été supprimées";
                $this->sendSuccessMessage($msg, self::ACTION_SUPPRIMER_STAGES);

                $this->getObjectManager()->clear();
                $session = $this->getSessionStageFromRoute();
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }

        $stages = $session->getStages()->toArray();
        $stages = array_filter($stages, function (Stage $stage) use ($session, $validator) {
            return $validator->assertSupprimer($session, $stage);
        });

        return new ViewModel([
            'title' => $title,
            'session' => $session,
            'stages' => $stages,
        ]);
    }

    /***
     * Partie concernant les stages des étudiants
     */
    const ROUTE_MES_STAGES = "mes-stages";
    const ROUTE_MON_STAGE = "mon-stage";

    const ACTION_MES_STAGES = "mes-stages";
    const ACTION_MON_STAGE = "mon-stage";

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function mesStagesAction() : ViewModel
    {
        $etudiant = $this->getEtudiantFromUser();
        return new ViewModel(['etudiant' => $etudiant]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function monStageAction() : ViewModel
    {
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }



}