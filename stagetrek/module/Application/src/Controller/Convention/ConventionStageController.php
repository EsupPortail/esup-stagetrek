<?php

namespace Application\Controller\Convention;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ConventionStage;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Convention\HasConventionStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Form\Convention\Traits\ConventionStageFormAwareTrait;
use Application\Service\ConventionStage\Traits\ConventionStageServiceAwareTrait;
use Application\Service\ConventionStage\Traits\ModeleConventionStageServiceAwareTrait;
use Application\Service\Renderer\MacroService;
use Exception;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;
use UnicaenRenderer\Service\Macro\MacroServiceAwareTrait;

class ConventionStageController extends AbstractActionController
{
    //
    const ROUTE_AFFICHER = "convention/afficher";
    const ROUTE_TELEVERSER = "convention/televerser";
    const ROUTE_GENERER = "convention/generer";
    const ROUTE_SUPPRIMER = "convention/supprimer";
    const ROUTE_TELECHARGER = "convention/telecharger";

    const ACTION_AFFICHER = "afficher";
    const ACTION_TELEVERSER = "televerser";
    const ACTION_GENERER = "generer";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_TELECHARGER = "telecharger";

    const EVENT_TELEVERSER = "event-televerser-convention";
    const EVENT_GENERER = "event-generer-convention";
    const EVENT_MODIFIER = "event-modifier-convention";
    const EVENT_SUPPRIMER = "event-supprimer-convention";

    use HasConventionStageTrait;
    use HasValidationStageTrait;
    use ConventionStageServiceAwareTrait;
    use ModeleConventionStageServiceAwareTrait;
    use MacroServiceAwareTrait;
    use ConventionStageFormAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction() : ViewModel
    {
        /** @var Stage $stage */
        $stage = $this->getStageFromRoute();
        return new ViewModel(['stage' => $stage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function televerserAction(): ViewModel
    {
        $title = "Téléverser la convention de stage";

        $fileService = $this->getConventionStageService()->getFileService();
        if(!isset($fileService)){
            $msg = "Le service de gestion des fichiers n'est pas correctement configuré.";
            return $this->failureAction($title, $msg);
        }


        $stage = $this->getStageFromRoute();
        $form = $this->getConventionStageTeleversementForm();
        $convention = $stage->getConventionStage();
        if(!$convention){
            $convention = new ConventionStage();
            $convention->setStage($stage);
        }
        $form->bind($convention);
        if ($this->params()->fromPost()) {
            /** @var Request $request */
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $fileData = $request->getFiles()->toArray();
                if(!empty($fileData)){
                    $fileData = $fileData['conventionStage']['file'];
                }
                try {
                    /** @var ConventionStage $convention */
                    $convention = $form->getData();
                    $convention = $this->getConventionStageService()->createFromFile($convention, $fileData);
                    $msg = "La convention de stage a été téléversé";
                    $this->sendSuccessMessage($msg);
                    $form->bind($convention);
                } catch (Exception $e) {
                    throw $e;
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'convention' => $convention, 'stage' => $stage]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function genererAction(): ViewModel
    {
        $title = "Générer la convention de stage";
        $fileService = $this->getConventionStageService()->getFileService();
        if(!isset($fileService)){
            $msg = "Le service de gestion des fichiers n'est pas correctement configuré.";
            return $this->failureAction($title, $msg);
        }

        $stage = $this->getStageFromRoute();
        $convention = $stage->getConventionStage();
        if(!$convention){
            $convention = new ConventionStage();
            $convention->setStage($stage);
        }
        $affectation = $stage->getAffectationStage();
        $terrain = ($affectation) ? $affectation->getTerrainStage() : null;
        $modele = ($terrain) ? $terrain->getModeleConventionStage() : null;
        if(!isset($terrain)){
            $msg = "Le terrain d'affectation du stage n'est pas définie";
            return $this->failureAction($title,$msg);
        }
        if(!isset($modele)){
            $msg = sprintf("Le terrain de stage %s n'est pas associé à un modéle de convention de stage", $terrain->getLibelle());
            return $this->failureAction($title,$msg);
        }
        $convention->setModeleConventionStage($modele);

        //On pré-génére le contenue
        $convention = $this->getConventionStageService()->pregenererConventionRendu($convention, $modele);
        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment générer la convention de stage ?";
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
               $convention = $this->getConventionStageService()->createFromTemplate($convention);
                $msg = "La convention de stage a été générée";
                $this->sendSuccessMessage($msg);
                return $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form, 'stage' => $stage, 'modele' => $modele, 'convention' => $convention]);
    }


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function supprimerAction() : ViewModel
    {
        $title = "Supprimer la convention";
        /** @var ConventionStage $convention */
        $stage = $this->getStageFromRoute();
        $convention = $stage->getConventionStage();
        $service = $this->getConventionStageService();

        $form = $this->getConfirmationForm();
        $question = "Voulez-vous vraiment supprimer la convention de stage ?";

        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($convention);
                $msg = "La convention de stage a été supprimée.";
                $this->sendSuccessMessage($msg);
                $form->addMessage($msg, Messenger::SUCCESS);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function telechargerAction() : ViewModel
    {
        $title = "Telecharger la convention";
        $fileService = $this->getConventionStageService()->getFileService();
        if(!isset($fileService)){
            $msg = "Le service de gestion des fichiers n'est pas correctement configuré.";
            return $this->failureAction($title, $msg);
        }

        /** @var ConventionStage $convention */
        $stage = $this->getStageFromRoute();
        $convention = $stage->getConventionStage();
        try {
            $this->getConventionStageService()->exportToPdf($convention);
        }
        catch (Exception $e){
            $msg = $e->getMessage();
            return $this->failureAction('Convention de stage', $msg);
        }
        return $this->failureAction($title, "Impossible d'acceder à la convention de stage");
    }



}