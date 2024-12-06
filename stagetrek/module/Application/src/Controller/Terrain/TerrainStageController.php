<?php

namespace Application\Controller\Terrain;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ContrainteCursusPortee;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Application\Exceptions\ImportException;
use Application\Form\Misc\ImportForm;
use Application\Form\Misc\Traits\ImportFormAwareTrait;
use Application\Form\TerrainStage\Traits\TerrainStageFormAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusServiceAwareTrait;
use Application\Service\TerrainStage\Traits\CategorieStageServiceAwareTrait;
use Application\Service\TerrainStage\Traits\TerrainStageServiceAwareTrait;
use Application\Validator\Import\TerrainStageCsvImportValidator;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Laminas\Http\Request;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;

/**
 * Class TerrainStageController
 * @package Application\Controller\terrains
 */
class TerrainStageController extends AbstractActionController
{
    use HasTerrainStageTrait;

    const ROUTE_INDEX = 'terrain';
    const ROUTE_LISTER = 'terrain/lister';
    const ROUTE_AFFICHER = 'terrain/afficher';
    const ROUTE_AJOUTER = 'terrain/ajouter';
    const ROUTE_MODIFIER = 'terrain/modifier';
    const ROUTE_SUPPRIMER = 'terrain/supprimer';
    const ROUTE_IMPORTER = 'terrain/importer';
    //TODO  : a revoir pour les contaacts
    const ROUTE_LISTER_CONTACT = 'terrain/contacts/lister';
    const ROUTE_AFFICHER_MODELE_CONVENTION = 'terrain/modele-convention/afficher';

    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_IMPORTER = "importer";
    const ACTION_LISTER_CONTACTS = "lister-contacts";
    const ACTION_AFFICHER_MODELE_CONVENTION = "afficher-modele-convention";

    const EVENT_AJOUTER = "event-ajouter-terrain-stage";
    const EVENT_IMPORTER = "event-importer-terrains-stages";
    const EVENT_MODIFIER = "event-modifier-terrain-stage";
    const EVENT_SUPPRIMER = "event-supprimer-terrain-stage";

    use TerrainStageServiceAwareTrait;
    use CategorieStageServiceAwareTrait;
    use ContrainteCursusServiceAwareTrait;

    use TerrainStageFormAwareTrait;

    //Actions qui permet de regrouper les terrains et les terrains associés sur une seul page

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction() : ViewModel
    {
        $terrains = $this->getTerrainStageService()->findAll();
        return new ViewModel(['terrains' => $terrains]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $terrains = $this->getTerrainStageService()->findAll();
        return new ViewModel(['terrains' => $terrains]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction() : ViewModel
    {
        $terrain = $this->getTerrainStageFromRoute();
        //Recherche des contraintes de cursus sur le terrain de stage
        $contraintes1 = $this->getContrainteCursusService()->findAllBy(['contrainteCursusPortee' => ContrainteCursusPortee::ID_PORTEE_GENERAL], ['dateFin' => 'desc','dateDebut' => 'desc',]);
        $contraintes2 = $this->getContrainteCursusService()->findAllBy(['contrainteCursusPortee' => ContrainteCursusPortee::ID_PORTEE_TERRAIN, 'terrainStage' => $terrain->getId()], ['dateFin' => 'desc','dateDebut' => 'desc',]);
        $contraintes3 = $this->getContrainteCursusService()->findAllBy(['contrainteCursusPortee' => ContrainteCursusPortee::ID_PORTEE_CATEGORIE, 'categorieStage' => $terrain->getCategorieStage()->getId()], ['dateFin' => 'desc','dateDebut' => 'desc',]);
        $contraintes2 = array_merge($contraintes1, $contraintes2);
        $contraintes = array_merge($contraintes2, $contraintes3);

        return new ViewModel(['terrain' => $terrain, 'contraintes' => $contraintes]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter un terrain de stage";
        $form = $this->getAddTerrainStageForm();
        $form->bind(new TerrainStage());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var TerrainStage $terrain */
                    $terrain = $form->getData();
                    $this->getTerrainStageService()->add($terrain);
                    $msg = "Le terrain de stage a été ajouté";
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
        $title = "Modifier le terrain de stage";
        /** @var TerrainStage $terrain */
        $terrain = $this->getTerrainStageFromRoute();

        $terrainService = $this->getTerrainStageService();
        $form = $this->getEditTerrainStageForm();
        $form->bind($terrain);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var TerrainStage $terrain */
                    $terrain = $form->getData();
                    $terrainService->update($terrain);
                    $msg = "Le terrain de stage a été modifié";
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
    public function supprimerAction() : ViewModel
    {
        $title = "Supprimer le terrain de stage";
        /** @var TerrainStage $terrain */
        $terrain = $this->getTerrainStageFromRoute();
        $terrainService = $this->getTerrainStageService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer le terrain de stage %s ?",
            $terrain->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $terrainService->delete($terrain);
                $msg = "Le terrain de stage a été supprimée";
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
    public function listerContactsAction() : ViewModel
    {
        $terrain = $this->getTerrainStageFromRoute();
        return new ViewModel(['terrain' => $terrain]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherModeleConventionAction(): ViewModel
    {
        $terrain = $this->getTerrainStageFromRoute();
        return new ViewModel(['terrain' => $terrain]);
    }


    use ImportFormAwareTrait;
    use ImportValidatorTrait;

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function importerAction() : ViewModel
    {
        $title = "Importer des terrains de stages";
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
                    $this->getTerrainStageService()->importFromCSV($fileData);
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
}