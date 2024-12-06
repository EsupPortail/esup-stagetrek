<?php

namespace Application\Controller\Affectation;

use Application\Controller\Misc\Interfaces\AbstractActionController;
use Application\Entity\Db\ConventionStage;
use Application\Entity\Db\ProcedureAffectation;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Convention\HasConventionStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Form\Affectation\Traits\ProcedureAffectationFormAwareTrait;
use Application\Form\Convention\Traits\ConventionStageFormAwareTrait;
use Application\Service\Affectation\Traits\ProcedureAffectationServiceAwareTrait;
use Application\Service\ConventionStage\Traits\ConventionStageServiceAwareTrait;
use Application\Service\ConventionStage\Traits\ModeleConventionStageServiceAwareTrait;
use Application\Service\Renderer\MacroService;
use Exception;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\View\Model\ViewModel;
use UnicaenApp\View\Helper\Messenger;
use UnicaenRenderer\Service\Macro\MacroServiceAwareTrait;

class ProcedureAffectationController extends AbstractActionController
{
    //
    const ROUTE_INDEX = "parametre/procedure-affectation";
    const ROUTE_LISTER = "parametre/procedure-affectation/lister";
    const ROUTE_AFFICHER = "parametre/procedure-affectation/afficher";
    const ROUTE_MODIFIER = "parametre/procedure-affectation/modifier";

    const ACTION_INDEX = "index";
    const ACTION_LISTER = "lister";
    const ACTION_AFFICHER = "afficher";
    const ACTION_MODIFIER = "modifier";

    const EVENT_MODIFIER = "event-modifier-procedure";

    use ProcedureAffectationServiceAwareTrait;
    use ProcedureAffectationFormAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function indexAction() : ViewModel
    {
        $proceduresAffectations = $this->getProcedureAffectationService()->findAll();
        return new ViewModel(['proceduresAffectations' => $proceduresAffectations]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function listerAction() : ViewModel
    {
        $proceduresAffectations = $this->getProcedureAffectationService()->findAll();
        return new ViewModel(['proceduresAffectations' => $proceduresAffectations]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function afficherAction() : ViewModel
    {
        $title = "Fiche de la procédure d'affectation";
        /** @var ProcedureAffectation $procedureAffectation */
        $procedureAffectation = $this->getProcedureAffectationFromRoute();
        return new ViewModel(['title' => $title, 'procedureAffectation' => $procedureAffectation]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function modifierAction(): ViewModel
    {
        /** @var ProcedureAffectation $procedure */
        $procedure = $this->getProcedureAffectationFromRoute();
        $title = sprintf("Modifier la procédure %s", $procedure->getLibelle());

        $procedureService = $this->getProcedureAffectationService();

        $form = $this->getEditProcedureAffectationForm();
        $form->bind($procedure);
        if ($data = $this->params()->fromPost()) {
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    /** @var ProcedureAffectation $procedure */
                    $procedure = $form->getData();
                    $procedureService->update($procedure);
                    $msg = sprintf("La procédure d'affectation %s a été modifé.", $procedure->getLibelle());
                    $this->sendSuccessMessage($msg);
                    return $this->successAction($title, $msg);
                } catch (Exception $e) {
                    return $this->failureAction($title, null, $e);
                }
            }
        }
        return new ViewModel(['title' => $title, 'form' => $form]);
    }

}