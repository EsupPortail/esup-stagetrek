<?php

namespace Application\Controller\Affectation;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Form\Affectation\Fieldset\AffectationStageFieldset;
use Application\Form\Affectation\Traits\AffectationStageFormAwareTrait;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\Affectation\Traits\ProcedureAffectationServiceAwareTrait;
use Application\Service\Contact\Traits\ContactStageServiceAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusEtudiantServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Preference\Traits\PreferenceServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Evenement\Service\MailAuto\Traits\MailAutoEvenementServiceAwareTrait;
use Exception;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

//Controller qui gére les affectations des terrains de stages

/**
 * Class AffectationController
 * @package Application\Controller\Stages
 *
 * @method ZfcUserAuthentication zfcUserAuthentication()
 */
class AffectationController extends AbstractActionController
{
    use HasEtudiantTrait;

    use AffectationStageServiceAwareTrait;
    use EtudiantServiceAwareTrait;
    use PreferenceServiceAwareTrait;
    use SessionStageServiceAwareTrait;
    use ContrainteCursusEtudiantServiceAwareTrait;
    use ContactStageServiceAwareTrait;
    use ProcedureAffectationServiceAwareTrait;

    //Formulaires
    use AffectationStageFormAwareTrait;


    /** Routes */
    const ROUTE_AFFICHER = "affectation/afficher";
    const ROUTE_MODIFIER = "affectation/modifier";
    const ROUTE_LISTER = "affectations/lister";
    const ROUTE_MODIFIER_AFFECTATIONS = "affectations/modifier";
    const ROUTE_CALCULER_AFFECTATIONS = "affectations/calculer";
    const ROUTE_EXPORTER = "affectations/exporter";
    /** Actions */
    const ACTION_LISTER = "lister";
    const ACTION_AFFICHER = "afficher";
    const ACTION_MODIFIER = "modifier";
    const ACTION_MODIFIER_AFFECTATIONS = "modifier-affectations";
    const ACTION_CALCULER_AFFECTATIONS = "calculer-affectations";
    const ACTION_EXPORTER = "exporter";

    /** Events */
    const EVENT_MODIFIER = "event-modifier-affectation";

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction(): ViewModel
    {
        $affectationStage = $this->getAffectationStageFromRoute();
        $title = "Affectation de ".$affectationStage->getEtudiant()->getDisplayName();

        return new ViewModel(['title' => $title, 'affectationStage' => $affectationStage]);
    }

    //Garder pour la forme mais probabelement jamais appelé

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $sessionStage = $this->getSessionStageFromRoute();
        $affectationsStages = $sessionStage->getAffectations()->toArray();
        return new ViewModel(['affectationsStages' => $affectationsStages]);
    }

    use HasValidationStageTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction(): ViewModel
    {
        /** @var AffectationStage $affectationStage */
        $affectationStage = $this->getAffectationStageFromRoute();
        $etudiant = $affectationStage->getEtudiant();

        $title = sprintf("Modifier l'affectation de %s", $etudiant->getDisplayName());

        $form = $this->getEditAffectationStageForm();
        $form->bind($affectationStage);
        $form->setAffectationStage($affectationStage);

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var AffectationStage $affectationStage */
                    $affectationStage = $form->getData();
                    $this->getAffectationStageService()->update($affectationStage);
                    $msg = sprintf("L'affectationStage de stage de %s a été modifiée",
                        $etudiant->getDisplayName()
                    );
                    $this->sendSuccessMessage($msg);
                    //Rechargement des données
                    $this->getObjectManager()->clear();
                    $affectationStage = $this->getAffectationStageFromRoute();
//                    Pour le rafraichissement des badges dans le selectpicker
                    $form->bind($affectationStage);
                    $form->setAffectationStage($affectationStage);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
                $sendMail = (isset($data['affectationStage'][AffectationStageFieldset::SEND_MAIL])
                    && boolval($data['affectationStage'][AffectationStageFieldset::SEND_MAIL]));
                if ($sendMail){
                    try {
                        $event = $this->getMailAutoAffectationEvenementService()->create($affectationStage->getStage());
                        $this->getMailAutoAffectationEvenementService()->traiter($event);
                    } catch (Exception $e) {
                        return $this->failureAction($title, null, $e);
                    }
                }

            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'affectationStage' => $affectationStage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAffectationsAction(): ViewModel
    {
        $title = "Modifier les affectations de stages";
        $sessionStage = $this->getSessionStageFromRoute();

        if ($data = $this->params()->fromPost()) {
            if(isset($data['preValidations']) || isset($data['validations'])) {
                $affectationsUpdated=[];
                /** @var Stage $stage */
                foreach ($sessionStage->getStages() as $stage){
                    $affectation = $stage->getAffectationStage();
                    if(isset($data['preValidations'][$affectation->getId()])){
                        $preValidation = boolval($data['preValidations'][$affectation->getId()]);
                        if($affectation->isPreValidee() != $preValidation){
                            $affectationsUpdated[$affectation->getId()] = $affectation;
                            $affectation->setPreValidee($preValidation);
                        }
                    }
                    if(isset($data['validations'][$affectation->getId()])){
                        $validation = boolval($data['validations'][$affectation->getId()]);
                        if($affectation->isValidee() != $validation){
                            $affectationsUpdated[$affectation->getId()] = $affectation;
                            $affectation->setValidee($validation);
                            $terrain = $stage->getTerrainStage();
                            if($validation && !isset($terrain)){
                                $affectation->getStage()->setStageNonEffectue(true);
                            }
                        }
                        if(!$validation && $stage->isNonEffectue()){
                            $affectation->getStage()->setStageNonEffectue(false);
                        }
                    }
                }
                try {
                    $this->getAffectationStageService()->updateMultiple($affectationsUpdated);
                    $this->sendSuccessMessage("Les affectations de stages ont été mise à jours");
                    //Rechargement des données
                    $this->getObjectManager()->clear();
                    $sessionStage = $this->getSessionStageFromRoute();
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['sessionStage' => $sessionStage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function exporterAction(): ViewModel
    {
        $title = "Exporter les affectations";
        /** @var SessionStage $sessionStage */
        $sessionStage = $this->getSessionStageFromRoute();
        return new ViewModel(['title' => $title, 'sessionStage' => $sessionStage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function calculerAffectationsAction(): ViewModel
    {
        $title = "Calculer les affectations";
        /** @var SessionStage $sessionStage */
        $sessionStage = $this->getSessionStageFromRoute();

        //On regarde s'il existe au moins 1 affectations non validé
        $canExec = false;
        /** @var AffectationStage $affectationStage */
        foreach ($sessionStage->getAffectations() as $affectationStage){
            if(!$affectationStage->isPreValidee() && !$affectationStage->isValidee()){
                $canExec = true;
                break;
            }
        }
        if(!$canExec){
            if(!empty($affectationStage->getStage())){
                $msg = "Toutes les affectations de stage sont validées ou pré-validées.";
                $msg .= "<br/> Exécuter la procédure d'affectation n'est pas utile.";
            }
            else{
                $msg = "La session n'as aucun stage à affecter.";
            }
            return $this->successAction($title, $msg, null, Messenger::WARNING);
        }

        $procedure = $this->getProcedureAffectationService()->getProcedureCourante();
        if(!isset($procedure)){
            $msg = "La procédure d'affectation à utiliser n'est pas correctement configurée.";
            return $this->failureAction($title, $msg);
        }

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment executer la procédure d'affectations des stages ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getProcedureAffectationService()->run($procedure, $sessionStage);
                $msg = "Les affectations ont été calculées";
                $this->sendSuccessMessage($msg);
                $form->addMessage($msg, Messenger::SUCCESS);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'sessionStage' => $sessionStage, 'procedureAffectation' => $procedure ]);
    }

    //Pour gerer les mails automatique lors de la validation d'une affectation par la commission
    use MailServiceAwareTrait;
    //a voir
    use MailAutoEvenementServiceAwareTrait;


}