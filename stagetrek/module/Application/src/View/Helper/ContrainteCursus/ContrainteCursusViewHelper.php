<?php


namespace Application\View\Helper\ContrainteCursus;

use Application\Controller\Contrainte\ContrainteCursusController as Controller;
use Application\Entity\Db\ContrainteCursus;
use Application\Entity\Traits\Contraintes\HasContrainteCursusTrait;
use Application\Form\Contrainte\ContrainteCursusForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ParametrePrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;

/**
 * Class ContrainteCursusViewHelper
 * @package Application\View\Helper\ContrainteCursus
 */
class ContrainteCursusViewHelper extends AbstractEntityActionViewHelper
{
    use HasContrainteCursusTrait;

    /**
     * @param ContrainteCursus|null $contrainte
     * @return self
     */
    public function __invoke(ContrainteCursus $contrainte = null): static
    {
        $this->contrainteCursus = $contrainte;
        return $this;
    }


    /**
     * @param ContrainteCursusForm|null $form
     * @return string
     */
    public function renderForm($form) : string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/contrainte/contrainte-cursus/forms/form-contrainte-cursus', $params);
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['contraintesCursus' => $entities], $params);
        return $this->getView()->render('application/contrainte/contrainte-cursus/listes/liste-contraintes-cursus', $params);
    }


    /**************************
     * Liens pour les actions *
     **************************/
    function actionAllowed(string $action): bool
    {

        $ressources = new ArrayRessource();
        if($this->hasContrainteCursus()){$ressources->add(ContrainteCursus::RESOURCE_ID, $this->getContrainteCursus());}
        return match ($action) {
            Controller::ACTION_LISTER => $this->hasPrivilege(ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AFFICHER),
            Controller::ACTION_AJOUTER => $this->hasPrivilege(ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasContrainteCursus() && $this->hasPrivilege(ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AJOUTER),
            Controller::ACTION_SUPPRIMER => $this->hasContrainteCursus() && $this->hasPrivilege(ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_SUPPRIMER),
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
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une contrainte";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_AJOUTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['contrainteCursus' => $this->getContrainteCursus()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la contrainte de cursus";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER,  ['contrainteCursus' => $this->getContrainteCursus()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la contrainte";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}