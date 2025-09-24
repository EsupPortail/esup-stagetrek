<?php


namespace Application\Controller\Etudiant;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\Etudiant;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Groupe\HasGroupeTrait;
use Application\Form\Etudiant\Traits\EtudiantFormAwareTrait;
use Application\Form\Etudiant\Traits\ImportEtudiantFormAwareTrait;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsForm;
use Application\Form\Referentiel\Interfaces\ImportEtudiantsFormInterface;
use Application\Form\Referentiel\Traits\ImportEtudiantsFormsAwareTrait;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Referentiel\Interfaces\ImportEtudiantsServiceInterface;
use Application\Service\Referentiel\Traits\ReferentielPromoServiceAwareTrait;
use Application\Service\Referentiel\Traits\ReferentielsEtudiantsServicesAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use ArrayObject;
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
    public function indexAction(): ViewModel
    {
        $form = $this->getEtudiantRechercheForm();
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            $criteria = [];
            if ($form->isValid()) {
                $criteria = array_filter($data, function ($v) {
                    return !empty($v);
                });
            }
            if (!empty($criteria)) {
                $etudiants = $this->getEtudiantService()->search($criteria);
            } else {
                $etudiants = $this->getEtudiantService()->findAll();
            }
        } else {
            $etudiants = $this->getEtudiantService()->findAll();
        }
        return new ViewModel(['form' => $form, 'etudiants' => $etudiants]);
    }

    public function afficherAction(): ViewModel
    {
        $etudiant = $this->getEtudiantFromRoute();
        return new ViewModel(['etudiant' => $etudiant]);
    }


    public function afficherInfosAction(): ViewModel
    {
        $etudiant = $this->getEtudiantFromRoute();
        return new ViewModel(['etudiant' => $etudiant]);
    }

    public function listerStagesAction(): ViewModel
    {
        $etudiant = $this->getEtudiantFromRoute();
        return new ViewModel(['etudiant' => $etudiant]);
    }

    public function ajouterAction(): ViewModel
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
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    public function modifierAction(): ViewModel
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
                    $msg = sprintf("Le profil de %s a été modifié.", $etudiant->getDisplayName());
                    $this->sendSuccessMessage($msg);
                    $form->bind($etudiant);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'etudiant' => $etudiant]);
    }

    public function supprimerAction(): ViewModel
    {
        $title = "Supprimer l'étudiant";
        $etudiant = $this->getEtudiantFromRoute();
        /** @var Etudiant $etudiant */
        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vraiment supprimer l'étudiant.e %s de l'application ?", $etudiant->getDisplayName());
        $form->setConfirmationQuestion($question);
        if ($this->actionConfirmed()) {
            try {
                $this->getEtudiantService()->delete($etudiant);
                $msg = sprintf("Le profil de %s a été supprimé.", $etudiant->getDisplayName());
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }


    use ImportValidatorTrait;
    use GroupeServiceAwareTrait;
    use HasGroupeTrait;
    use ImportEtudiantFormAwareTrait;

    /** TODO : a revoir */
    use ReferentielPromoServiceAwareTrait;
    use ReferentielsEtudiantsServicesAwareTrait;

    use ImportEtudiantsFormsAwareTrait;



    public function importerAction(): ViewModel
    {
        $title = "Importer des étudiants";
        $forms = $this->getImportEtudiantsForms();
        $services = $this->getReferentielsEtudiantsServices();
        if (empty($forms) || empty($services)) {
            return $this->renderError($title, "Aucun service d'import des étudiants n'est définie");
        }
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $key = ($data[AbstractImportEtudiantsForm::INPUT_KEY])?? null;
            if(!isset($key)) {
                throw new Exception("Impossible de déterminer le type d'import à effectuer");
            }
            /** @var AbstractImportEtudiantsForm $formActif */
            $formActif = $this->getImportEtudiantsForm($key);
            if(isset($formActif)){
                $formActif->setActif(true);
                $formActif->bind(new ArrayObject($data));
                if ($formActif->isValid()) {
                    $data = $formActif->getData()->getArrayCopy();
                    try{
                        $sourceCode = ($data[$formActif::INPUT_SOURCE]) ?? 'N/A';
                        $importService = $this->getReferentielEtudiantService($sourceCode);
                        if(!isset($importService) || !isset($importService)) {
                            throw new Exception("Impossible de déterminer le service de gestion de l'import des étudiants pour la source demandée");
                        }
                        if(!$importService instanceof ImportEtudiantsServiceInterface){
                            throw new Exception("Le service ne permet pas l'import d'étudiant");
                        }
                        $etudiants = $importService->importer($data);
                        $log = $importService->renderLogs();
                        $type = $importService->getLogType();
                        if(isset($log)) {
                            $this->flashMessenger()->addMessage($log, self::ACTION_IMPORTER . Messenger::NAMESPACED_SEVERITY_SEPARATOR . $type);
                        }
                    }
                    catch(Exception $e){
                        $log = (isset($importService)) ? $importService->renderLogs(): null;
                        if(!isset($log)){$log = $e->getMessage();}
                        return $this->renderError($title, $log);
                    }
                }
            }
        }
        //Calcul du formulaire actif par défaut
        if(!isset($formActif)){
            $form = current($forms);
            if(isset($form) && $form instanceof ImportEtudiantsFormInterface){
                $form->setActif(true);
            }
        }

        return new ViewModel(['title' => $title, 'forms'=>$forms]);
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