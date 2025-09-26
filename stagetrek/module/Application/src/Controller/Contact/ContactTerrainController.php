<?php

namespace Application\Controller\Contact;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Application\Exceptions\ImportException;
use Application\Form\Contacts\Traits\ContactTerrainFormAwareTrait;
use Application\Form\Misc\Traits\ImportFormAwareTrait;
use Application\Service\Contact\Traits\ContactTerrainServiceAwareTrait;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use Exception;
use Laminas\Http\Request;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;

class ContactTerrainController extends AbstractActionController
{
    use HasContactTrait;
    use ContactTerrainFormAwareTrait;
    use ContactTerrainServiceAwareTrait;
    use HasTerrainStageTrait;

    const ROUTE_LISTER = 'contact/terrain/lister';
    const ROUTE_AJOUTER = 'contact/terrain/ajouter';
    const ROUTE_MODIFIER = 'contact/terrain/modifier';
    const ROUTE_SUPPRIMER = 'contact/terrain/supprimer';
//    TODO
    const ROUTE_IMPORTER = 'contact/terrain/importer';
    const ROUTE_EXPORTER = 'contact/terrain/exporter';

    /** ACTIONS */
    const ACTION_AFFICHER = "afficher";
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";
//    TODO
    const ACTION_IMPORTER = "importer";
    const ACTION_EXPORTER = "exporter";

    /** EVENTS */
    const EVENT_AJOUTER = "event-ajouter-contact-terrain";
    const EVENT_IMPORTER = "event-importer-contact-terrains";
    const EVENT_MODIFIER = "event-modifier-contact-terrain";
    const EVENT_SUPPRIMER = "event-supprimer-contact-terrain";

    public function listerAction(): ViewModel
    {
        $contact = $this->getContactFromRoute();
        if(!isset($contact)){
            return $this->failureAction("Liste des terrains associés aux contacts", "Le contact demandé n'as pas été trouvé");
        }
        $contactsTerrains = $contact->getContactsTerrains()->toArray();
        return new ViewModel(['contact'=>$contact, 'contactsTerrains' => $contactsTerrains]);
    }

    public function ajouterAction(): ViewModel
    {
        $title = "Associer un contact à un terrain de stage";
        $contact = $this->getContactFromRoute();
        $terrain = $this->getTerrainStageFromRoute();
        if(!isset($contact) && !isset($terrain)){//Logiquement géré par l'assertion
            return $this->failureAction($title, "Aucun contact ou terrain de stage n'ont été trouvée");
        }
        if(isset($contact) && !isset($terrain)){
            $title = "Associer le contact à un terrain de stage";
        }
        else if(!isset($contact) && isset($terrain)){
            $title = "Associer le terrain de stage à un contact";
        }

        $form = $this->getAddContactTerrainForm();
        $ct = new ContactTerrain();
        $ct->setContact($contact);
        $ct->setTerrainStage($terrain);
        $form->setContactTerrain($ct);

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ContactTerrain $ct */
                    $ct = $form->getData();
                    /** @var ContactTerrain $ct */
                    $ct = $this->getContactTerrainService()->add($ct);
                    $msg = sprintf("Le contact %s a été associé au terrain %s", $ct->getDisplayName(), $ct->getTerrainStage()->getLibelle());
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }

    public function modifierAction(): ViewModel
    {
        $title = "Modifier les actions du contact sur le terrain de stage";
        /** @var ContactTerrain $contactTerrain */
        $contactTerrain = $this->getContactTerrainFromRoute();
        $contactTerrainService = $this->getContactTerrainService();
        $form = $this->getEditContactTerrainForm();
        $form->setContactTerrain($contactTerrain);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ContactTerrain $contactTerrain */
                    $contactTerrain = $form->getData();
//                    var_dump($data);
//                    var_dump($contactTerrain->sendMailAutoValidationStage());
//                    var_dump($contactTerrain->sendMailAutoRappelValidationStage());
//                    die();
                    $contactTerrainService->update($contactTerrain);
                    $msg = "Les actions par défaut du contact sur le terrain on a été modifiés";
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }


    public function supprimerAction(): ViewModel
    {
        $title = "Dissocier le contact du terrain de stage";
        /** @var ContactTerrain $contactTerrain */
        $contactTerrain = $this->getContactTerrainFromRoute();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer dissocier le contact du terrain?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getContactTerrainService()->delete($contactTerrain);
                $msg = "Le contact n'est plus associé au terrain de stage.";
                $form->addMessage($msg, Messenger::SUCCESS);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form, 'contactTerrain' => $contactTerrain]);
    }

    /** TODO : faire un tait d'import/export */
    use ImportFormAwareTrait;
    use ImportValidatorTrait;

    public function importerAction() : ViewModel
    {
        $title = "Importer des contacts de terrains";
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
                    $this->getContactTerrainService()->importFromCSV($fileData);
                    $msg = "L'importation des contacts est terminée";
                    $this->flashMessenger()->addMessage($msg,  self::ACTION_IMPORTER . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::SUCCESS);
                }
                catch (ImportException) {
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

    public function exporterContactsTerrainsAction(): array
    {
        var_dump("Pas encore implémenté : exporter");
        return [];
    }
}