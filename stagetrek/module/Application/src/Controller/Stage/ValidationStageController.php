<?php

namespace Application\Controller\Stage;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Stage;
use Application\Entity\Db\ValidationStage;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Form\Stages\Traits\ValidationStageFormAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Application\Service\Stage\Traits\ValidationStageServiceAwareTrait;
use Application\Validator\Actions\Traits\ValidationStageValidatorAwareTrait;
use DateTime;
use Evenement\Service\MailAuto\Traits\MailAutoEvenementServiceAwareTrait;
use Exception;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use UnicaenUtilisateur\Entity\Db\User;

/**
 * Class SessionStageController
 * @package Application\Controller
 *
 * @method FlashMessenger flashMessenger()
 */
class ValidationStageController extends AbstractActionController
{
    //Notes : contrairements aux sessions et autres entités, les stages ne peuvent êtres ajouté/supprimé qu'en groupes ou automatiquement
    //Les modifications d'un stage ce font via d'autres entités (Session, affectation, validation ...)

    const ROUTE_AFFICHER = "stage/validation/afficher";
    const ROUTE_VALIDER = "stage/validation/valider";
    const ROUTE_MODIFIER = "stage/validation/modifier";

    const ACTION_AFFICHER = "afficher";
    const ACTION_VALIDER = "valider";
    const ACTION_MODIFIER = "modifier";

    const EVENT_MODIFIER = "event-modifier-validation-stage";

    use StageServiceAwareTrait;
    use HasEtudiantTrait;

    use ValidationStageFormAwareTrait;
    use ValidationStageServiceAwareTrait;
    use MailAutoEvenementServiceAwareTrait;

    use ValidationStageValidatorAwareTrait;




    public function afficherAction() : ViewModel
    {
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage, 'vueEtudiante' => $this->currentRoleIsStudent()]);
    }

    public function validerAction() : ViewModel
    {
        $title = "Validation du stage";
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        $token = $this->getTokenValidationFromRoute();

        //On regarde différent car pouvant amené a des messages d'erreur


        $validator = $this->getValidationStageValidator();
        if (!$validator->isValid($stage, ['stage' => $stage, 'token' => $token])) {
            $msg = $validator->getValidationNotAllowedMessage(['stage' => $stage]);
            $priority = $validator->getValidationNotAllowedMessagePriority();
//              Choix fait d'envoyer le message d'erreur par mail
//            if(isset($code_erreur)){
//                $msg .= sprintf("<hr> <span class='text-small text-muted'>Erreur %s</span>",$code_erreur);
//            }
            if ($validator->getSendMailErreurValidation()) {
                $this->getValidationStageService()->sendMailEchecValidationStage($stage, $token,
                    $validator->getCodeErreurValidation(),
                    $msg
                );
            }
            return $this->failureAction($title, $msg, null, null, $priority);
        }
        /** @var ContactStage $contact */
        $contact = null;
        /** @var ContactStage $contactStage */
        foreach ($stage->getContactsStages() as $contactStage) {
            $contactToken = $contactStage->getTokenValidation();
            if (strcmp(($contactToken) ?? "", $token) == 0) {
                $contact = $contactStage;
                break;
            }
        }
        if (!$contact) { //Au cas ou, logiquement on a l'erreur avant
            $validator->setCodeErreurValidation($validator::CODE_ERREUR_CONTACT_TOKEN_NOT_FOUND);
            return $this->failureAction($title, $validator->getValidationNotAllowedMessage());
        }

        $form = $this->getValidationStageForm();
        $form->bind($stage);
        $validateBy = ($contact->getDisplayName() && $contact->getDisplayName() != "") ? $contact->getDisplayName() : $contact->getEmail();
        if ($validateBy == "") {
            $validateBy = "Indéterminé";
        }
        $form->setValidateBy($validateBy);

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Stage $stage */
                    $stage = $form->getData();
                    $validationStage = $stage->getValidationStage();
                    /** @var ValidationStage $validationStage */
                    $validationStage = $this->getValidationStageService()->update($validationStage);
                    $etudiant = $stage->getEtudiant();
                    $msg = sprintf("Le stage de <strong>%s</strong> a bien été <strong>%s</strong>.
                        <div>Merci de votre participation.</div>",
                        $etudiant->getDisplayName(),
                        ($validationStage->isValide()) ? "validé" : "non validé"
                    );
                } catch (Exception $e) {
                    $msg = "Une erreur est survenue lors de la validation du stage.";
                    $msg .= "<br/>Merci de réessayer ultérieurement.";
                    $subMessage = "Si le problème persiste merci de contacter votre scolarité";
                    return $this->failureAction($title, $msg, $e, $subMessage);
                }
                try{
                    $evenement = $this->getMailAutoStageValidationEffectueEvenementService()->create($stage);
                    $this->getMailAutoStageValidationEffectueEvenementService()->traiter($evenement);
                }
                catch (Exception ){
                } // Cas d'une erreur hors de la validation
                return $this->successAction($title, $msg);
            }
        }
        return new ViewModel([
            'title' => $title,
            'stage' => $stage,
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    public function modifierAction() : ViewModel
    {
        $title = "Modifier la validation du stage";
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        $form = $this->getValidationStageForm(true);
        $form->bind($stage);

        $validationStage = $stage->getValidationStage();
        $validateBy = ($validationStage) ? $validationStage->getValidateBy() : null;
        if(!isset($validateBy) || $validateBy == null){
            /** @var User $user */
            $user = $this->getUser();
            $validateBy = $user->getDisplayName();
        }
        $form->setValidateBy($validateBy);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Stage $stage */
                    $stage = $form->getData();
                    $validationStage = $stage->getValidationStage();
                    $this->getValidationStageService()->update($validationStage);
                    $msg = "La validation du stage a été modifiée avec succès";
                    $this->sendEditSuccessMessage($msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel([
            'title' => $title,
            'stage' => $stage,
            'form' => $form,
        ]);
    }

    /** @return string|null */
    protected function getTokenValidationFromRoute() : ?string
    {
        return $this->getParam('token');
    }
}