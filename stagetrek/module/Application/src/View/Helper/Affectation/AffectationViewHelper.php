<?php


namespace Application\View\Helper\Affectation;

use Application\Controller\Affectation\AffectationController as Controller;
use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\SessionStage;
use Application\Entity\Traits\Stage\HasAffectationStageTrait;
use Application\Entity\Traits\Stage\HasSessionStageTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\StagePrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Application\View\Helper\Interfaces\EtudiantActionViewHelperInterface;
use Application\View\Helper\Interfaces\Implementation\EtudiantActionViewHelperTrait;
use Laminas\Form\Form;

/**
 * Class AffectationViewHelper
 * @package Application\View\Helper\Stages
 */
class AffectationViewHelper extends AbstractEntityActionViewHelper
    implements EtudiantActionViewHelperInterface
{
    use EtudiantActionViewHelperTrait;
    use HasAffectationStageTrait;
    use HasSessionStageTrait;

    /**
     * @param AffectationStage|null $affectation
     * @return self
     */
    public function __invoke(AffectationStage $affectation = null): static
    {
        $this->affectationStage = $affectation;
        return $this;
    }

    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['affectationsStages' => $entities], $params);
        return $this->getView()->render('application/affectation/affectation/listes/liste-affectations', $params);
    }


    function renderForm(Form $form): string
    {  $params = ['form' => $form, 'affectationStage' => $this->getAffectationStage()];
        return $this->getView()->render('application/affectation/affectation/forms/form-affectation', $params);
    }

    public function renderInfos(): string
    {
        $params = ['affectationStage' => $this->getAffectationStage(),  'vueEtudiante' => $this->vueEtudianteActive()];
        return $this->getView()->render('application/affectation/affectation/partial/affectation-infos',$params);
    }

    public function renderCouts(): string
    {
        $params = ['affectationStage' => $this->getAffectationStage(),  'vueEtudiante' => $this->vueEtudianteActive()];
        return $this->getView()->render('application/affectation/affectation/partial/affectation-couts', $params);
    }

    public function renderPreferences(): string
    {
        $params = ['affectationStage' => $this->getAffectationStage(),  'vueEtudiante' => $this->vueEtudianteActive()];
        return $this->getView()->render('application/affectation/affectation/partial/affectation-preferences',$params);
    }

    public function renderComplementsInfos(): string
    {
        $params = ['affectationStage' => $this->getAffectationStage(),  'vueEtudiante' => $this->vueEtudianteActive()];
        return $this->getView()->render('application/affectation/affectation/partial/affectation-complements-infos', $params);
    }
    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        $ressource = new ArrayRessource();
        if($this->hasAffectationStage()){$ressource->add(AffectationStage::RESOURCE_ID, $this->getAffectationStage());}
        if($this->hasSessionStage()){$ressource->add(SessionStage::RESOURCE_ID, $this->getSessionStage());}
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->hasAffectationStage() && $this->hasPrivilege(StagePrivileges::AFFECTATION_AFFICHER),
            Controller::ACTION_LISTER, Controller::ACTION_EXPORTER
                => $this->hasSessionStage() && $this->hasPrivilege(StagePrivileges::AFFECTATION_AFFICHER),
            Controller::ACTION_MODIFIER => $this->hasAffectationStage() && $this->hasPrivilege(StagePrivileges::AFFECTATION_MODIFIER),
            Controller::ACTION_MODIFIER_AFFECTATIONS => $this->hasSessionStage() && $this->hasPrivilege(StagePrivileges::AFFECTATION_MODIFIER),
            Controller::ACTION_CALCULER_AFFECTATIONS => $this->hasSessionStage() && $this->callAssertion($ressource, StagePrivileges::AFFECTATION_RUN_PROCEDURE),
            default => false,
        };
    }
    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['affectationStage' => $this->getAffectationStage()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AFFICHER, Icone::AFFICHER);
        $attributes['title'] = ($attributes['title']) ??  "Afficher l'affectation de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-secondary ajax-modal";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $affectation = $this->getAffectationStage();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['affectationStage' => $affectation->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier l'affectation du stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifierAffectations(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER_AFFECTATIONS)) {
            return "";
        }
        $session = $this->getSessionStage();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER_AFFECTATIONS, ['sessionStage' => $session->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier les affecations de la sessions";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienExporter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_EXPORTER)) {
            return "";
        }
        $session = $this->getSessionStage();
        $url = $this->getUrl(Controller::ROUTE_EXPORTER, ['sessionStage' => $session->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::EXPORTER, Icone::EXPORTER);
        $attributes['title'] = ($attributes['title']) ??"Exporter les affectations";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }
}