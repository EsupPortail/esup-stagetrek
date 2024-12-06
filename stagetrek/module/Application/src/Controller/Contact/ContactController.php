<?php

namespace Application\Controller\Contact;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Contact;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Exceptions\ImportException;
use Application\Form\Contacts\Traits\ContactFormAwareTrait;
use Application\Form\Misc\Traits\ImportFormAwareTrait;
use Application\Service\Contact\Traits\ContactServiceAwareTrait;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use Exception;
use Laminas\Http\Request;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;

/**
 * Class ContactController
 * @package Application\Controller\Contacts
 */
class ContactController extends AbstractActionController
{
    use HasContactTrait;
    use ContactServiceAwareTrait;
    use ContactFormAwareTrait;

    /** ROUTES */
    const ROUTE_INDEX = 'contact';
    const ROUTE_LISTER = 'contact/lister';
    const ROUTE_AFFICHER = 'contact/afficher';
    const ROUTE_AFFICHER_INFOS = 'contact/infos';
    const ROUTE_AJOUTER = 'contact/ajouter';
    const ROUTE_MODIFIER = 'contact/modifier';
    const ROUTE_SUPPRIMER = 'contact/supprimer';
    const ROUTE_IMPORTER = 'contact/importer';
//    const ROUTE_EXPORTER = 'contact/exporter';


    /** ACTIONS */
    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AFFICHER_INFOS = "afficher-infos";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_IMPORTER = "importer";
//    TODO
    const ACTION_EXPORTER = "exporter";

    /** EVENTS */
    const EVENT_AJOUTER= "event-ajouter-contact";
    const EVENT_IMPORTER = "event-importer-contact";
    const EVENT_MODIFIER = "event-modifier-contact";
    const EVENT_SUPPRIMER = "event-supprimer-contact";


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\DBAL\Exception
     */
    public function indexAction() : ViewModel
    {
        $form = $this->getContactRechercheForm();
        $codeVisible = false;
        if ($data = $this->params()->fromQuery()) {
            if(isset($data[$form::INPUT_AFFICHER_CODE])){
                $codeVisible = ($data[$form::INPUT_AFFICHER_CODE] == 1);
            }
            $form->setData($data);
            $criteria = array_filter($data, function ($v) {
                return !empty($v);
            });
            if (!empty($criteria)) {
                $contacts = $this->getContactService()->search($criteria);
            } else {
                $contacts = $this->getContactService()->findAllBy(['actif'=>true]);
            }
        } else {
            //Par défaut on n'affiche que les actifs
            $contacts = $this->getContactService()->findAllBy(['actif'=>true]);
            $form->setData(['actif' => true]);
        }
        return new ViewModel(['form' => $form, 'contactsStages' => $contacts, 'codeVisible' =>$codeVisible]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $contacts = $this->getContactService()->findAll();
        return new ViewModel(['contacts' => $contacts]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter un contact";

        $contact = new Contact();
        $form = $this->getAddContactForm();
        $form->bind($contact);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var Contact $contact */
                $contact = $form->getData();
                try {
                    $this->getContactService()->add($contact);
                    $msg = "Le contact a été ajoutée";
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
    public function afficherAction() : ViewModel
    {
        $title = "Fiche du contact";
        $contact = $this->getContactFromRoute();
        if (!isset($contact)) {
            return $this->failureAction($title, "Le contact demandé n'as pas été trouvé");
        }
        return new ViewModel(['title' => $title, 'contact'=>$contact]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherInfosAction() : ViewModel
    {
        $contact = $this->getContactFromRoute();
        return new ViewModel(['contact'=>$contact]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction() : ViewModel
    {
        $title = "Modifier le contact";
        $contact = $this->getContactFromRoute();

        $form = $this->getEditContactForm();
        $form->bind($contact);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                /** @var Contact $contact */
                $contact = $form->getData();
                try {
                    $this->getContactService()->update($contact);
                    $msg = "Le contact a été modifié";
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
    public function supprimerAction() : ViewModel
    {
        $title = "Supprimer le contact";
        $contact = $this->getContactFromRoute();
        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer le contact ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getContactService()->delete($contact);
                $msg = "Le contact a bien été supprimé";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form, 'contact' => $contact]);
    }

    /** TODO : faire un tait d'import/export */
    use ImportFormAwareTrait;
    use ImportValidatorTrait;

    public function importerAction() : ViewModel
    {
        $title = "Importer des contacts";
        $form = $this->getImportForm();

        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    $fileData = $data[$form::INPUT_IMPORT_FILE];
                    $importvalidator = $this->getImportValidator();
                    if(!$importvalidator->isValid($fileData)){
                        throw new ImportException("Le fichier d'import n'est pas valide");
                    }
                    $this->getContactService()->importFromCSV($fileData);
                    $msg = "L'importation des terrains de stages est terminée";
                    $this->flashMessenger()->addMessage($msg,  self::ACTION_IMPORTER . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::SUCCESS);
                }
                catch (ImportException $e) {
                    $msg = $importvalidator->getNotAllowedImportMessage();
                    $this->flashMessenger()->addMessage($msg, self::ACTION_IMPORTER . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::ERROR);
                } catch (Exception $e) {
                    $msg = "Une erreur est survenue : ".$e->getMessage();
                    $this->flashMessenger()->addMessage($msg, self::ACTION_IMPORTER . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::ERROR);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' =>$form]);
    }

    public function exporterContactsAction(): ViewModel
    {
        return $this->failureAction("Export", "Fonction pas encore implémenté");
    }
}