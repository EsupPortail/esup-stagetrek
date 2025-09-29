<?php


namespace Application\View\Helper\Convention;

use Application\Controller\Convention\ConventionStageController as Controller;
use Application\Entity\Db\ConventionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Convention\HasConventionStageTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Form\Convention\ConventionStageForm;
use Application\Form\Convention\ConventionStageTeleversementForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ConventionsPrivileges;
use Application\Service\ConventionStage\Traits\ConventionStageServiceAwareTrait;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

class ConventionViewHelper extends AbstractEntityActionViewHelper
{
    use HasConventionStageTrait;
    use HasValidationStageTrait;
    use HasStageTrait;

    /**
     * @return Stage|null
     */
    public function getStage(): ?Stage
    {
        if($this->hasStage()){return $this->stage;}
        if($this->hasConventionStage()){return $this->conventionStage->getStage();}
        return null;
    }

    /**
     * @param ConventionStage|null $convention
     * @return self
     */
    public function __invoke(ConventionStage $convention = null): static
    {
        $this->conventionStage = $convention;
        if(isset($convention)){
            $this->stage = $convention->getStage();
        }
        return $this;
    }

    public function render() : string
    {
        $stage = $this->getStage();
        if(!$stage){return "";}
        $params =['stage'=> $stage];
        return $this->getView()->render("application/convention/convention-stage/partial/fiche-convention", $params);
    }

    /**
     * @return string
     */
    public function renderSignataires(): string
    {
        $stage = $this->getStage();
        if(!$stage){return "";}
        $params =['stage'=> $stage];
        return $this->getView()->render("application/convention/convention-stage/listes/liste-signataires-convention", $params);

    }

    /**
     * @param Form|null $form
     * @return string
     */
    public function renderTeleversementForm(Form $form): string
    {
        $params=['form'=> $form];
        return $this->getView()->render('application/convention/convention-stage/forms/form-televerser-convention-stage', $params);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    function actionAllowed(string $action): bool
    {
        $ressources = new ArrayRessource();
        if($this->hasStage()){ $ressources->add(Stage::RESOURCE_ID, $this->getStage());}
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->hasStage() && $this->hasPrivilege(ConventionsPrivileges::CONVENTION_AFFICHER),
            Controller::ACTION_TELEVERSER => $this->hasStage() && $this->hasPrivilege(ConventionsPrivileges::CONVENTION_TELEVERSER),
            Controller::ACTION_GENERER => $this->hasStage() && $this->callAssertion($ressources, ConventionsPrivileges::CONVENTION_GENERER),
            Controller::ACTION_TELECHARGER => $this->hasStage() && $this->callAssertion($ressources, ConventionsPrivileges::CONVENTION_TELECHARGER),
            Controller::ACTION_SUPPRIMER => $this->hasStage() && $this->callAssertion($ressources, ConventionsPrivileges::CONVENTION_SUPPRIMER),
            default => false,
        };
    }

    public function lienTeleverser(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_TELEVERSER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_TELEVERSER,  ['stage' => $this->getStage()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::TELEVERSER, Icone::TELEVERSER);
        $attributes['title'] = ($attributes['title']) ?? "Télèverser la convention de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_TELEVERSER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienGenerer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_GENERER)) {
            return "";
        }

        $data = ["stage" => $this->getStage()->getId()];
        $url = $this->getUrl(Controller::ROUTE_GENERER,  $data, [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::render(Icone::render(Icone::RUN_PROCESS)), "Générer");
        $attributes['title'] = ($attributes['title']) ?? "Générer la convention de stage depuis un modéle";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_GENERER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER,  ['stage' => $this->getStage()->getId()], [], true);
        $libelle = Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la convention de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienTelecharger(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_TELECHARGER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_TELECHARGER,  ['stage' => $this->getStage()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::TELECHARGER, Icone::TELECHARGER);
        $attributes['title'] = ($attributes['title']) ?? "Télécharger la convention de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success";

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    use ConventionStageServiceAwareTrait;
    public function getConventionRenduConntainsMacro() : bool
    {
        if(!isset($this->conventionStage)){return false;}
        return $this->getConventionStageService()->getConventionRenduContainsMacro($this->conventionStage);
    }
}