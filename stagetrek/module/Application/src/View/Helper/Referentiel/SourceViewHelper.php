<?php

namespace Application\View\Helper\Referentiel;

use Application\Entity\Db\Groupe;
use Application\Entity\Db\Source;
use Application\Entity\Traits\Referentiel\HasSourceTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ReferentielPrivilege;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Application\Controller\Referentiel\SourceController as Controller;
use Laminas\Form\Form;

class SourceViewHelper extends AbstractEntityActionViewHelper
{
    use HasSourceTrait;
    /**
     * @param Source|null $source
     * @return self
     */
    public function __invoke(?Source $source=null) : self
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->render();
    }

    public function render() : string
    {
        if(!isset($this->source)){
            return '<span class="badge badge-muted">Non Définie</span>';
        }
        return sprintf('<span class="badge source-%s">%s</span>', mb_strtolower($this->source->getCode()), $this->source->getLibelle());
    }

    public function renderListe(?array $entities = [], ?array $params = []): string
    {
        $params = array_merge(['sources' => $entities], $params);
        return $this->getView()->render('application/referentiel/source/listes/liste-sources',$params);
    }

    function renderForm(Form $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render("application/referentiel/source/forms/form-source", $params);
    }

    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasSource()){$ressources->add(Source::RESOURCE_ID, $this->getSource());}

        return match ($action) {
            Controller::ACTION_AJOUTER => $this->hasPrivilege(ReferentielPrivilege::REFERENTIEL_SOURCE_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasSource() && $this->callAssertion($ressources, ReferentielPrivilege::REFERENTIEL_SOURCE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasSource() && $this->callAssertion($ressources, ReferentielPrivilege::REFERENTIEL_SOURCE_SUPPRIMER),
            default => false,
        };
    }


    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une source de données";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $source = $this->getSource();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['source' => $source->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ?? "Modifier la source de données";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_MODIFIER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $source = $this->getSource();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['source' => $source->getId()], [], true);
        $libelle = Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la source de données";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}