<?php


namespace Application\View\Helper\Disponibilite;

use Application\Controller\Etudiant\DisponibiliteController as Controller;
use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Entity\Traits\Etudiant\HasDisponibiliteTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Form\Etudiant\DisponibiliteForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use DateTime;

/**
 * Class DisponibiliteViewHelper
 * @package Application\View\Helper\Disponibilite
 */
class DisponibiliteViewHelper extends AbstractEntityActionViewHelper
{
    use HasEtudiantTrait;
    use HasDisponibiliteTrait;

    /**
     * @param Disponibilite|null $disponibilite
     * @return self
     */
    public function __invoke(Disponibilite $disponibilite = null): static
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['disponibilites' => $entities], $params);
        return $this->getView()->render('application/etudiant/disponibilite/listes/liste-disponibilites', $params);
    }

    /**
     * @param DisponibiliteForm|null $form
     * @return string
     */
    public function renderForm($form) : string
    {
        $params=['form'=> $form];
        return $this->getView()->render('application/etudiant/disponibilite/forms/form-disponibilite', $params);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    function actionAllowed(string $action): bool
    {
        $ressources = new ArrayRessource();
        if($this->hasEtudiant()){ $ressources->add(Etudiant::RESOURCE_ID, $this->getEtudiant());}
        if($this->hasDisponibilite()){ $ressources->add(Disponibilite::RESOURCE_ID, $this->getDisponibilite());}
        return match ($action) {
            Controller::ACTION_LISTER => $this->hasEtudiant() && $this->hasPrivilege(EtudiantPrivileges::DISPONIBILITE_AFFICHER),
            Controller::ACTION_AJOUTER => $this->hasEtudiant() && $this->hasPrivilege(EtudiantPrivileges::DISPONIBILITE_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasDisponibilite() && $this->hasPrivilege(EtudiantPrivileges::DISPONIBILITE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasDisponibilite() && $this->hasPrivilege(EtudiantPrivileges::DISPONIBILITE_SUPPRIMER),
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
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une disponibilité";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        $libelle = ($libelle) ?? sprintf("%s ajouter", Icone::AJOUTER);
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['disponibilite' => $this->getDisponibilite()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la disponibilité";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['disponibilite' => $this->getDisponibilite()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la disponibilité";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }


}