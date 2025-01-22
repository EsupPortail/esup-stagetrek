<?php


namespace Application\Controller\Etudiant;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Groupe;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Groupe\HasGroupeTrait;
use Application\Exceptions\ImportException;
use Application\Form\Etudiant\ImportEtudiantForm;
use Application\Form\Etudiant\Traits\EtudiantFormAwareTrait;
use Application\Form\Etudiant\Traits\ImportEtudiantFormAwareTrait;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Referentiel\Traits\ReferentielPromoServiceAwareTrait;
use Application\Service\Referentiel\Traits\ReferentielServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Application\Validator\Import\EtudiantCsvImportValidator;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Laminas\Http\Request;
use laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Class EtudiantController
 * @package Application\Controller\Etudiants
 *
 * Note : Par soucis de lisibilité du codes, les fonctions des différentes actions sont réparties dans les différents traits EtudiantXxxx_ActionsTraits
 *
 * @method FlashMessenger flashMessenger()
 * @method ZfcUserAuthentication zfcUserAuthentication()
 */
class EtudiantController extends AbstractActionController
{
    use HasEtudiantTrait;

    use EtudiantServiceAwareTrait;
    use AnneeUniversitaireServiceAwareTrait;
    use SessionStageServiceAwareTrait;
    use StageServiceAwareTrait;
    use AffectationStageServiceAwareTrait;
    use ContrainteCursusServiceAwareTrait;

    use EtudiantFormAwareTrait;
    use ConfirmationFormAwareTrait;


    /**
     * Partie pour l'administration des étudiants
     */
    const ROUTE_INDEX = 'etudiant';
    const ROUTE_AFFICHER = 'etudiant/afficher';
    const ROUTE_AFFICHER_INFOS = 'etudiant/afficher/infos';
    const ROUTE_LISTER_STAGES = 'etudiant/stages/lister';
    const ROUTE_AJOUTER = 'etudiant/ajouter';
    const ROUTE_IMPORTER = 'etudiant/importer';
    const ROUTE_MODIFIER = 'etudiant/modifier';
    const ROUTE_SUPPRIMER = 'etudiant/supprimer';

    const ACTION_INDEX = "index";
    const ACTION_RECHERCHER = "rechercher";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AFFICHER_INFOS = "afficher-infos";
    const ACTION_LISTER_STAGES = "lister-stages";
    const ACTION_AJOUTER = 'ajouter';
    const ACTION_IMPORTER = 'importer';
    const ACTION_MODIFIER = 'modifier';
    const ACTION_SUPPRIMER = 'supprimer';

    const EVENT_AJOUTER = "event-ajouter-etudiant";
    const EVENT_IMPORTER = "event-importer-etudiants";
    const EVENT_MODIFIER = "event-modifier-etudiant";
    const EVENT_SUPPRIMER = "event-supprimer-etudiant";


    const EVENT_IMPORTER_ETUDIANTS = "event-importer-etudiant";

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\DBAL\Exception
     */
    public function indexAction() : ViewModel
    {
        $form = $this->getEtudiantRechercheForm();
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            $criteria = [];
            if($form->isValid()) {
                $criteria = array_filter($data, function ($v) {
                    return !empty($v);
                });
            }
            if (!empty($criteria)) {
                $etudiants = $this->getEtudiantService()->search($criteria);
            } else {
                $etudiants = $this->getEtudiantService()->findAll();
            }
        }
        else {
            $etudiants = $this->getEtudiantService()->findAll();
        }

        return new ViewModel(['form' => $form, 'etudiants' => $etudiants]);
    }

    public function afficherAction(): ViewModel
    {
        $etudiant = $this->getEtudiantFromRoute();

        return new ViewModel(['etudiant' => $etudiant]);
    }


    public function afficherInfosAction() : ViewModel
    {
        $etudiant = $this->getEtudiantFromRoute();
        return new ViewModel(['etudiant' => $etudiant]);
    }

    public function listerStagesAction() : ViewModel
    {
        $etudiant = $this->getEtudiantFromRoute();
        return new ViewModel(['etudiant' => $etudiant]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter un étudiant";

        $form = $this->getAddEtudiantForm();
        $form->bind(new Etudiant());

        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {

                try {
                    /** @var Etudiant $etudiant */
                    $etudiant = $form->getData();
                    $this->getEtudiantService()->add($etudiant);
                    $msg = sprintf("Le profil de %s a été créé.", $etudiant->getDisplayName());
                    $this->sendSuccessMessage($msg);
                    $form->bind($etudiant);
//                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }

    public function modifierAction() : ViewModel
    {
        $title = "Modifier l'étudiant";
        /** @var Etudiant $etudiant */
        $etudiant = $this->getEtudiantFromRoute();

        $form = $this->getEditEtudiantForm();
        $form->bind($etudiant);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var Etudiant $etudiant */
                    $etudiant = $form->getData();
                    $this->getEtudiantService()->update($etudiant);
                    $msg = sprintf("Le profil de %s a été modifé.", $etudiant->getDisplayName());
                    $this->sendSuccessMessage($msg);
                    $form->bind($etudiant);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form, 'etudiant'=>$etudiant]);
    }

    public function supprimerAction() : ViewModel
    {
        $title = "Supprimer l'étudiant";
        $etudiant = $this->getEtudiantFromRoute();

        /** @var Etudiant $etudiant */
        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment supprimer l'étudiant.e %s de l'application ?",
            $etudiant->getDisplayName()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $this->getEtudiantService()->delete($etudiant);
                $msg = sprintf("Le profil de %s a été supprimé.",
                    $etudiant->getDisplayName()
                );
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }


    use ImportValidatorTrait;

    use GroupeServiceAwareTrait;
    use HasGroupeTrait;
    use ImportEtudiantFormAwareTrait;

    /** TODO : a revoir */
    use ReferentielPromoServiceAwareTrait;
    use ReferentielServiceAwareTrait;
//

    public function importerAction(): ViewModel
    {
        $title = "Importer des étudiants";

        /** @var Groupe $groupe */
        $groupe = $this->getGroupeFromRoute();

        $form = $this->getImportEtudiantForm($groupe);
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            //Pour avoir le bon onglet sur la page indépendament de la validité du formulaire
            $importReferentiel = (boolval($data[ImportEtudiantForm::INPUT_IMPORT_REFERENTIEL]));
            $importFromGroupe = (boolval($data[ImportEtudiantForm::INPUT_IMPORT_GROUPE]));
            $importFromFile =  (boolval($data[ImportEtudiantForm::INPUT_IMPORT_FILE])) && ($data[ImportEtudiantForm::INPUT_IMPORT_FILE]['name'] != "");
            $data[ImportEtudiantForm::INPUT_CURRENT_IMPORT] = match (true) {
                $importFromFile => ImportEtudiantForm::INPUT_IMPORT_FILE,
                $importFromGroupe => ImportEtudiantForm::INPUT_IMPORT_GROUPE,
                default => ImportEtudiantForm::INPUT_IMPORT_REFERENTIEL,
            };
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    $referentielId = intval(($data[ImportEtudiantForm::INPUT_IMPORT_REFERENTIEL]) ?? 0);
                    $etudiants = [];
                    $success = false;
                    //Import depuis un référentiel
                    if ($importReferentiel) {
                        /** @var \Application\Entity\Db\ReferentielPromo $referentiel */
                        $referentiel = $this->getReferentielPromoService()->find($referentielId);
                        if(!isset($referentiel)) {
                            throw new ImportException("Le référentiel demandé n'as pas été trouvé");
                        }
                        $etudiants = $this->getEtudiantImportService()->importEtudiantFromReferentiel($referentiel);
                        $success = true;
                    }
                    else if ($importFromFile) {
                        $fileData = $data[ImportEtudiantForm::INPUT_IMPORT_FILE];
                        /** @var EtudiantCsvImportValidator $importValidator */
                        $importValidator = $this->getImportValidator();
                        if ($importValidator->isValid($fileData)) {
                            $etudiants = $this->getEtudiantImportService()->importEtudiantFromCSV($fileData);
                            $success = true;
                        } else {
                            $msg = $importValidator->getNotAllowedImportMessage();
                            $this->sendWarningMessage($msg, self::ACTION_IMPORTER);
                        }
                    }
                    else {
                        $groupeId = intval($data[ImportEtudiantForm::INPUT_IMPORT_GROUPE], 0);
                        /** @var Groupe $groupe */
                        $groupe = $this->getGroupeService()->find($groupeId);
                        $etudiants = $groupe->getEtudiants()->toArray();
                        $success = true;
                    }
                    //Pour ne pas faire les groupes en cas d'echec;
                    if ($success) {
                        //Ajout dans le groupe
                        $groupeId = intval(($data[ImportEtudiantForm::INPUT_ADD_IN_GROUPE]) ?? 0);
                        /** @var Groupe $groupe */
                        $groupe = $this->getGroupeService()->find($groupeId);
                        if ((!empty($etudiants)) && $groupe) {
                            //TODO : a revoir si l'on garde ce pseudo-filtre / sécuréité
                            $etudiantsCanBeAdd = $this->getGroupeService()->findEtudiantsCanBeAddInGroupe($groupe);
                            $etudiantsCanBeAdd = new ArrayCollection($etudiantsCanBeAdd);
                            $etudiants = array_filter($etudiants, function (Etudiant $etudiant) use($etudiantsCanBeAdd){
                                return $etudiantsCanBeAdd->contains($etudiant);
                            });
                            $this->getGroupeService()->addEtudiants($groupe, $etudiants);

                            $this->getGroupeService()->update($groupe);
                        }
                        $this->getEtudiantService()->updateEtats($etudiants);
                        //Messages
                        $msg = "Import effectué";
                        $msg .= sprintf("<br/><span class='mx-3'>%s étudiant(s) ajouté(s) ou mis à jours</span>",
                            sizeof($etudiants)
                        );

                        $this->flashMessenger()->addMessage($msg, self::ACTION_IMPORTER . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::SUCCESS);
                    }
                } catch (ImportException $e) {
                    $this->flashMessenger()->addMessage($e->getMessage(), self::ACTION_IMPORTER . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::ERROR);
                } catch (Exception $e) {
                    return $this->failureAction($title, $e->getMessage()."<br/>". $e->getTraceAsString());
                }
            }
        }
        return new ViewModel(['title' => $title, 'form'=>$form]);
    }

    /**
     * Partie pour les étudiants
     */
    const ROUTE_MON_PROFIL = 'mon-profil';
    const ACTION_MON_PROFIL = 'mon-profil';

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function monProfilAction(): ViewModel
    {
        $user = $this->getUser();
        $etudiant = $this->getEtudiantService()->findOneBy(['user' => $user->getId()]);

        return new ViewModel(['etudiant' => $etudiant]);

    }

}