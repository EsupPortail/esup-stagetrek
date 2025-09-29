<?php


namespace Application\View\Helper\Affectation;

use Application\Controller\Affectation\ProcedureAffectationController as Controller;
use Application\Entity\Db\ProcedureAffectation;
use Application\Entity\Traits\Stage\HasProcedureAffectationTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\StagePrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class AffectationViewHelper
 * @package Application\View\Helper\Stages
 */
class ProcedureAffectationViewHelper extends AbstractEntityActionViewHelper
{
    use HasProcedureAffectationTrait;


    /**
     * @param ProcedureAffectation|null $procedureAffectation
     * @return self
     */
    public function __invoke(ProcedureAffectation $procedureAffectation = null): static
    {
        $this->procedureAffectation = $procedureAffectation;
        return $this;
    }

    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['proceduresAffectations' => $entities], $params);
        return $this->getView()->render('application/affectation/procedure-affectation/listes/liste-procedures-affectations', $params);
    }


    function renderForm(Form $form): string
    {  $params = ['form' => $form,];
        return $this->getView()->render('application/affectation/procedure-affectation/forms/form-procedure-affectation', $params);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        $ressource = new ArrayRessource();
        if($this->hasProcedureAffectation()){$ressource->add(ProcedureAffectation::RESOURCE_ID, $this->getProcedureAffectation());}
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->hasProcedureAffectation() && $this->hasPrivilege(StagePrivileges::PROCEDURE_AFFICHER),
            Controller::ACTION_LISTER => $this->hasPrivilege(StagePrivileges::PROCEDURE_AFFICHER),
            Controller::ACTION_MODIFIER => $this->hasProcedureAffectation() && $this->hasPrivilege(StagePrivileges::PROCEDURE_MODIFIER),
            default => false,
        };
    }

    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['procedureAffectation' => $this->getProcedureAffectation()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AFFICHER, Icone::AFFICHER);
        $attributes['title'] = ($attributes['title']) ??  "Afficher la procédure d'affectation";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-secondary ajax-modal";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $procedure = $this->getProcedureAffectation();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['procedureAffectation' => $procedure->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la procédure";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

}