<?php

namespace Application\Controller\Contact;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ContactStage;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Form\Contacts\Traits\ContactStageFormAwareTrait;
use Application\Provider\Mailing\CodesMailsProvider;
use Application\Service\Contact\Traits\ContactStageServiceAwareTrait;
use Exception;
use Laminas\Form\Element\Radio;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\View\Model\ViewModel;
use PHPUnit\Event\RuntimeException;
use UnicaenApp\View\Helper\Messenger;
use UnicaenMail\Entity\Db\Mail;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;

/**
 * Class ContactStageController
 * @package Application\Controller\Contacts
 */
class ContactStageController extends AbstractActionController
{
    /** Accés aux entités */
    use HasValidationStageTrait;
    use HasContactTrait;
    use HasEtudiantTrait;
    use ContactStageFormAwareTrait;
    use ContactStageServiceAwareTrait;

    const ROUTE_LISTER = 'contact/stage/lister';
    const ROUTE_AJOUTER = 'contact/stage/ajouter';
    const ROUTE_MODIFIER = 'contact/stage/modifier';
    const ROUTE_SUPPRIMER = 'contact/stage/supprimer';
    const ROUTE_SEND_MAIL_VALIDATION = 'contact/stage/mail-validation/envoyer';

    /** ACTIONS */

    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_SEND_MAIL_VALIDATION = "envoyer-mail-validation";
    /** EVENTS */
    const EVENT_AJOUTER = "event-ajouter-contact-stage";
    const EVENT_MODIFIER = "event-modifier-contact-stage";
    const EVENT_SUPPRIMER = "event-supprimer-contact-stage";
    const EVENT_SEND_LIEN_VALIDATION = "event-send-lien-validation";


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction(): ViewModel
    {
        $contact = $this->getContactFromRoute();
        $contactsStages = $contact->getContactsStages()->toArray();
        return new ViewModel(['contact'=>$contact, 'contactsStages' => $contactsStages]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function ajouterAction(): ViewModel
    {
        $title = "Associer le contact à un stage";
        $contact = $this->getContactFromRoute();
        $stage = $this->getStageFromRoute();

        $form = $this->getAddContactStageForm();
        $contactStage = new ContactStage();
        if(isset($contact)){
            $contactStage->setContact($contact);
            $form->setContact($contact);
        }
        if(isset($stage)){
            $contactStage->setStage($stage);
            $form->setStage($stage);
        }

        $form->bind($contactStage);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var ContactStage $contactStage */
                $contactStage = $form->getObject();
                try {
                    $contactStage = $this->getContactStageService()->add($contactStage);
                    $this->getContactStageService()->add($contactStage);
                    $msg = "Le contact a été ajoutée associé au stage succés.";
                    $this->sendSuccessMessage($msg);
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
        $title = "Modifier le contact";
        /** @var ContactStage $contact */
        $contactStage = $this->getContactStageFromRoute();
        if(!isset($contactStage)){
            return $this->failureAction($title, "Le contact demandé n'as pas été trouvé");
        }
        $contact = $contactStage->getContact();
        $stage = $contactStage->getStage();
        $contactStageService = $this->getContactStageService();
        $form = $this->getEditContactStageForm();
        $form->setStage($stage);
        //!!! important de définir le contact après le stage car la liste des contactsStages change
        $form->setContact($contact);
        $form->bind($contactStage);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ContactStage $contactStage */
                    $contactStage = $form->getData();
                    $contactStageService->update($contactStage);

                    $msg = $this->sendEditSuccessMessage();
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
    public function supprimerAction(): ViewModel
    {
        $title = "Dissocier le contact du stage";
        /** @var ContactStage $contactStage */
        $contactStage = $this->getContactStageFromRoute();
        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment dissocier le contact du stage ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getContactStageService()->delete($contactStage);
                $msg = "Le contact n'est plus associé au stage.";
                $form->addMessage($msg, Messenger::SUCCESS);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form, 'contactStage' => $contactStage]);
    }

    use MailServiceAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function envoyerMailValidationAction(): array|ViewModel
    {
        $title = "Mail de validation du stage";
        /** @var ContactStage $contactStage */
        $contactStage = $this->getContactStageFromRoute();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment envoyer par mail un lien pour la validation du stage ?";
        $form->setConfirmationQuestion($question);
        //Rajout d'un input pour la regénération d'un lien
        $tokenData = [
            'name' => 'generer-token',
            'type' => Radio::class,
            'options' => [
                'label' => 'Générer un nouveau lien de validation ?',
                'label_options' => [
                    'disable_html_escape' => true,
                ],
                'use_hidden_element' => true,
                'value_options' => [
                    '1' => '<span class="ms-1 me-3">Oui</span>',
                    '0' => '<span class="ms-1 me-3">Non</span>',
                ],
            ],
            'attributes' => [
                'id' => 'generer-token',
                'value' => ($contactStage->tokenValide()) ? 0 : 1,
                'disabled' => !$contactStage->tokenValide(),
            ]
        ];
        $form->addInputs('generer-token', $tokenData);

        if ($this->actionConfirmed()) {
            try {
                /** @var Request $request */
                $request = $this->getRequest();
                $data = $request->getPost();
                $genererToken = !$contactStage->tokenValide() || boolval(($data['generer-token']) ?? true);
                if($genererToken) {
                    $this->getContactStageService()->genererTokenValidation($contactStage);
                }
                $stage = $contactStage->getStage();
                $mailData = ['stage' => $stage, 'contactStage' => $contactStage];

                /** @var \Application\Service\Mail\MailService $mailService */
                $mailService = $this->getMailService();
                //On vérifie que l'on a bien tout
                $mailService->canSendMailType(CodesMailsProvider::VALIDATION_STAGE, $mailData);
                /** @var Mail $mail */
                $mail = $mailService->sendMailType(CodesMailsProvider::VALIDATION_STAGE, $mailData);
                $mailSend = (isset($mail) && $mail->getStatusEnvoi() == Mail::SUCCESS);
                if(!$mailSend){
                    return $this->failureAction("Echec de l'envoie du mail", $mail->getLog());
                }
                return compact('title', 'contactStage', 'mailSend');
            } catch (Exception|RuntimeException $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return compact('title', 'form', 'contactStage');
    }
}