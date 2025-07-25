<?php

namespace Application\Controller\Terrain;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\ContrainteCursusPortee;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Application\Exceptions\ImportException;
use Application\Form\Misc\Traits\ImportFormAwareTrait;
use Application\Form\TerrainStage\Traits\CategorieStageFormAwareTrait;
use Application\Service\TerrainStage\Traits\CategorieStageServiceAwareTrait;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use Exception;
use Laminas\Http\Request;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;

/**
 * Class CategorieStageController
 * @package Application\Controller\terrains
 *
 */
class CategorieStageController extends AbstractActionController
{
    use HasTerrainStageTrait;
    const ROUTE_INDEX = "categorie-stage";
    const ROUTE_LISTER = "categorie-stage/lister";
    const ROUTE_AFFICHER = "categorie-stage/afficher";
    const ROUTE_AJOUTER = "categorie-stage/ajouter";
    const ROUTE_MODIFIER = "categorie-stage/modifier";
    const ROUTE_SUPPRIMER = "categorie-stage/supprimer";
    const ROUTE_IMPORTER = "categorie-stage/importer";

    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AFFICHER = "afficher";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_IMPORTER = "importer";

    const EVENT_AJOUTER = "event-ajouter-categorie-stage";
    const EVENT_MODIFIER = "event-modifier-categorie-stage";
    const EVENT_SUPPRIMER = "event-supprimer-categorie-stage";
    const EVENT_IMPORTER = "event-importer-categories-stages";


    use CategorieStageServiceAwareTrait;

    /** FORMULAIRES */
    use CategorieStageFormAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction() : ViewModel
    {
        $categories = $this->getCategorieStageService()->findAll();
        return new ViewModel(['categoriesStages' => $categories]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $categoriesStages = $this->getCategorieStageService()->findAll();
        return new ViewModel(['categoriesStages' => $categoriesStages]);
    }

    public function afficherAction() : ViewModel
    {
        $categorieStage = $this->getCategorieStageFromRoute();
        return new ViewModel(['categorieStage' => $categorieStage]);
    }


    public function ajouterAction() : ViewModel

    {
        $title = "Ajouter une categorie de stage";
        $form = $this->getAddCategorieStageForm();
        $form->bind(new CategorieStage());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    $categorie = $form->getData();
                    $this->getCategorieStageService()->add($categorie);
                    $msg = "La catégorie de stage a été créée";
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
    public function modifierAction(): ViewModel
    {
        $title = "Modifier la categorie de stage";
        /** @var CategorieStage $categorie */
        $categorie = $this->getCategorieStageFromRoute();

        $categorieService = $this->getCategorieStageService();
        $form = $this->getEditCategorieStageForm();
        $form->bind($categorie);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    $categorie = $form->getData();
                    $categorieService->update($categorie);
                    $msg = "La catégorie de stage a été modifiée";
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
    public function supprimerAction(): ViewModel
    {
        $title = "Supprimer la catégorie de de stage";
        /** @var CategorieStage $categorie */
        $categorie = $this->getCategorieStageFromRoute();
        $categorieService = $this->getCategorieStageService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer la categorie de stage %s ?",
            $categorie->getLibelle()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $categorieService->delete($categorie);
                $msg = "La catégorie a été supprimée";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    use ImportFormAwareTrait;
    use ImportValidatorTrait;
    public function importerAction(): ViewModel
    {
        $title = "Importer des catégories de stages";
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
                    $this->getCategorieStageService()->importFromCSV($fileData);
                    $msg = "L'importation des catégories de stages est terminée";
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
}