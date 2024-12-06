<?php

namespace Application\Controller\Convention;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Traits\Convention\HasConventionStageTrait;
use Application\Form\Convention\Traits\ModeleConventionStageFormAwareTrait;
use Application\Service\ConventionStage\Traits\ModeleConventionStageServiceAwareTrait;
use Exception;
use Laminas\View\Model\ViewModel;

class ModeleConventionController extends AbstractActionController
{
    //
    const ROUTE_INDEX = "modele-convention";
    const ROUTE_AFFICHER = "modele-convention/afficher";
    const ROUTE_LISTER = "modeles-conventions/lister";
    const ROUTE_AJOUTER = "modele-convention/ajouter";
    const ROUTE_MODIFIER = "modele-convention/modifier";
    const ROUTE_SUPPRIMER = "modele-convention/supprimer";

    const ACTION_INDEX = "index";
    const ACTION_AFFICHER = "afficher";
    const ACTION_LISTER = "lister";
    const ACTION_AJOUTER = "ajouter";
    const ACTION_MODIFIER = "modifier";
    const ACTION_SUPPRIMER = "supprimer";

    const EVENT_AJOUTER = "event-ajouter-modele-convention";
    const EVENT_MODIFIER = "event-modifier-modele-convention";
    const EVENT_SUPPRIMER = "event-supprimer-modele-convention";

    use ModeleConventionStageServiceAwareTrait;
    use HasConventionStageTrait;
    use ModeleConventionStageFormAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction() : ViewModel
    {
        $modeles = $this->getModeleConventionStageService()->findAll();
        return new ViewModel(['modeles' => $modeles]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction() : ViewModel
    {
        $modele = $this->getModeleConventionStageFromRoute();
        return new ViewModel(['modele' => $modele]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $modeles = $this->getModeleConventionStageService()->findAll();
        return new ViewModel(['modeles' => $modeles]);
    }

    public function ajouterAction() : ViewModel
    {
        $title = "Ajouter un modéle de convention";

        $form = $this->getAddModeleConventionStageForm();
        $form->bind(new ModeleConventionStage());
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ModeleConventionStage $modele */
                    $modele = $form->getData();
                    $this->getModeleConventionStageService()->add($modele);
                    $msg = "Le modéle de convention a été créé";
                    $this->sendSuccessMessage($msg);
                    return  $this->successAction($title, $msg);
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
        $title = "Modifier le modéle de convention";
        /** @var ModeleConventionStage $modele */
        $modele = $this->getModeleConventionStageFromRoute();

        $form = $this->getEditModeleConventionStageForm();
        $form->bind($modele);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ModeleConventionStage $modele */
                    $modele = $form->getData();
                    $this->getModeleConventionStageService()->update($modele);
                    $msg = "Le modéle de convention a été modifié";
                    $this->sendSuccessMessage($msg);
                    return  $this->successAction($title, $msg);
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
        $title = "Supprimer le modéle de convention";
        /** @var ModeleConventionStage $modele */
        $modele = $this->getModeleConventionStageFromRoute();
        $service = $this->getModeleConventionStageService();

        $form = $this->getConfirmationForm();
        $question = sprintf("Voulez-vous vraiment supprimer le modéle de convention %s ?",
            $modele->getCode()
        );
        $form->setConfirmationQuestion($question);

        if ($this->actionConfirmed()) {
            try {
                $service->delete($modele);
                $msg = "Le modéle de convention a été supprimé";
                $this->sendSuccessMessage($msg);
                return  $this->successAction($title, $msg);
            } catch (Exception $e) {
                return $this->failureAction($title, null, $e);
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

}