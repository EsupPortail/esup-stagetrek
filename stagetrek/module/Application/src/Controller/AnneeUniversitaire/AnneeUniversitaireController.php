<?php

namespace Application\Controller\AnneeUniversitaire;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Form\Annees\Traits\AnneeUniversitaireFormAwareTrait;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Exception;
use Laminas\Http\Response;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

/**
 * Class AnneeUniversitaireController
 * @package Application\Controller
 *
 * @method FlashMessenger flashMessenger()
 */
class AnneeUniversitaireController extends AbstractActionController
{
    use HasAnneeUniversitaireTrait;

    use AnneeUniversitaireServiceAwareTrait;
    use SessionStageServiceAwareTrait;
    use GroupeServiceAwareTrait;

    // Formulaires
    use AnneeUniversitaireFormAwareTrait;


    /** ROUTES */
    const ROUTE_INDEX = "annee";
    const ROUTE_LISTER = "annee/lister";
    const ROUTE_AFFICHER = "annee/afficher";
    const ROUTE_AFFICHER_INFOS = "annee/afficher/infos" ;
    const ROUTE_AFFICHER_CALENDRIER = "annee/afficher/calendrier" ;
    const ROUTE_AJOUTER = "annee/ajouter";
    const ROUTE_MODIFIER = "annee/modifier";
    const ROUTE_SUPPRIMER = "annee/supprimer";
    const ROUTE_VALIDER = "annee/valider";
    const ROUTE_DEVEROUILLER_ANNEE = "annee/deverouiller";

    //TODO : a revoir pour splitter la création de l'année universitaire avec des étapes et une timeline
    /** ACTIONS */
    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AFFICHER_INFOS = "afficher-infos";
    const ACTION_AFFICHER_CALENDRIER = "afficher-calendrier";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_VALIDER = "valider";
    const ACTION_DEVEROUILLER = "deverouiller";

    /** Events */
    const EVENT_AJOUTER = "event-ajouter-anneeUniversitaire";
    const EVENT_MODIFIER = "event-modifier-anneeUniversitaire";
    const EVENT_SUPPRIMER = "event-supprimer-anneeUniversitaire";
    const EVENT_VALIDER = "event-valider-anneeUniversitaire";
    const EVENT_DEVEROUILLER = "event-deverouiller-anneeUniversitaire";

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction() : ViewModel|Response
    {
        $annees = $this->getAnneeUniversitaireService()->findAll();
        return new ViewModel(['annees' => $annees]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $annees = $this->getAnneeUniversitaireService()->findAll();
        return new ViewModel(['annees' => $annees]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction() : ViewModel
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getAnneeUniversitaireFromRoute();
        $id = $this->getParam('anneeUniversitaire');
        if(!isset($annee) && isset($id)){
            $msg = "L'année universitaire demandée n'as pas été trouvée";
            $this->sendErrorMessage($msg);
            $this->redirect()->toRoute(self::ROUTE_INDEX);
        }
        if(!isset($annee)) {
            $annee = $this->getAnneeUniversitaireService()->findAnneeCourante();
            if(!isset($annee)){
                $msg = "Il n'y a pas d'année universitaire actuellement en cours";
                $this->sendWarningMessage($msg);
                $this->redirect()->toRoute(AnneeUniversitaireController::ROUTE_LISTER);
            }
        }
        return new ViewModel(['annee' => $annee]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherInfosAction() : ViewModel
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getAnneeUniversitaireFromRoute();
        return new ViewModel(['annee' => $annee]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherCalendrierAction() : ViewModel
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getAnneeUniversitaireFromRoute();
        return new ViewModel(['annee' => $annee]);
    }


    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter une année";

        $form = $this->getAddAnneeUniversitaireForm();
        $form->bind(new AnneeUniversitaire());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var AnneeUniversitaire $annee */
                    $annee = $form->getData();
                    $annee = $this->getAnneeUniversitaireService()->add($annee);
                    $msg = sprintf("L'année universitaire %s a été créée ",
                        $annee->getLibelle()
                    );
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
    public function modifierAction() : ViewModel
    {
        $title = "Modifier l'année";
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getAnneeUniversitaireFromRoute();
        if($annee->isLocked()){
            $msg = sprintf("L'année %s est validée", $annee->getLibelle());
            return $this->failureAction($title, $msg);
        }

        $form = $this->getEditAnneeUniversitaireForm();
        $form->bind($annee);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var AnneeUniversitaire $annee */
                    $annee = $form->getData();
                    $this->getAnneeUniversitaireService()->update($annee);
                    $msg = sprintf("L'année %s a été modifiée.",
                        $annee->getLibelle()
                    );
                    $this->sendSuccessMessage($msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'annee' => $annee]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction() : ViewModel
    {
        $title = "Supprimer l'année";
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getAnneeUniversitaireFromRoute();
        $anneeService = $this->getAnneeUniversitaireService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer l'année universitaire %s ?",
            $annee->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $msg = sprintf("L'année %s a été supprimée.",$annee->getLibelle());
                $anneeService->delete($annee);
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function validerAction() : ViewModel
    {
        $title = "Valider l'année";
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getAnneeUniversitaireFromRoute();
        if($annee->isLocked()){
            $msg = sprintf("L'année %s est déjà validée", $annee->getLibelle());
            return $this->failureAction($title, $msg);
        }

        $anneeService = $this->getAnneeUniversitaireService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment valider l'année universitaire %s ?",
            $annee->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
//                $anneeService->validerAnnee($annee);
                $anneeService->lock($annee);
                $msg = sprintf("L'année %s a été validée.",
                    $annee->getLibelle()
                );
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'annee' => $annee]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function deverouillerAction() : ViewModel
    {
        $title = "Déverrouiller l'année";
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getAnneeUniversitaireFromRoute();
        if(!$annee->isLocked()){
            $msg = sprintf("L'année %s n'est pas validée", $annee->getLibelle());
            return $this->failureAction($title, $msg);
        }

        $anneeService = $this->getAnneeUniversitaireService();
        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment déverrouiller l'année universitaire %s ?",
            $annee->getLibelle()
        );

        $form->setConfirmationQuestion($question);
        if ($this->actionConfirmed()) {
            try {
                $anneeService->unlock($annee);
                $msg =  sprintf("L'année %s a été dévérouillée.",
                    $annee->getLibelle()
                );
                $backTo = self::ROUTE_AFFICHER."/".$annee->getId();
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg, );
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'annee' => $annee]);
    }
}