<?php

namespace Application\View\Helper\Referentiel;

use Application\Controller\Referentiel\ReferentielPromoController as Controller;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Traits\Referentiel\HasReferentielPromoTrait;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ReferentielPrivilege;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

class ReferentielPromoViewHelper extends AbstractEntityActionViewHelper
{
    use HasReferentielPromoTrait;

    /**
     * @param ReferentielPromo|null $referentielPromo
     * @return self
     */
    public function __invoke(?ReferentielPromo $referentielPromo=null) : self
    {
        $this->referentielPromo = $referentielPromo;
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
        if(!isset($this->referentielPromo)){
            return '<span class="badge badge-muted">Non Définie</span>';
        }
        $source = $this->referentielPromo->getSource();
        return sprintf('<span class="badge source-%s">%s</span>', mb_strtolower($source->getCode()), $this->getReferentielPromo()->getLibelle());
    }

    public function renderListe(?array $entities = [], ?array $params=[]): string
    {
        $params = array_merge(['referentielsPromos' => $entities], $params);
        return $this->getView()->render('application/referentiel/referentiel-promo/listes/liste-referentiels-promos',$params);
    }


    function renderForm(Form $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render("application/referentiel/referentiel-promo/forms/form-referentiel-promo", $params);
    }

    public function actionAllowed(string $action) : bool
    {
        return match ($action) {
            Controller::ACTION_AJOUTER => $this->hasPrivilege(ReferentielPrivilege::REFERENTIEL_PROMO_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasReferentielPromo() && $this->hasPrivilege(ReferentielPrivilege::REFERENTIEL_PROMO_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasReferentielPromo() && $this->hasPrivilege(ReferentielPrivilege::REFERENTIEL_PROMO_SUPPRIMER),
            default => false,
        };
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un référentiel de promo";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $referentiel = $this->getReferentielPromo();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['referentielPromo' => $referentiel->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ?? "Modifier le référentiel de promo";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_MODIFIER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $referentiel = $this->getReferentielPromo();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['referentielPromo' => $referentiel->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le référentiel de promo";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}