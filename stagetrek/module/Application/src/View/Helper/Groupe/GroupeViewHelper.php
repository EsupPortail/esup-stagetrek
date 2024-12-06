<?php


namespace Application\View\Helper\Groupe;

use Application\Controller\Groupe\GroupeController as Controller;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Entity\Traits\Groupe\HasGroupeTrait;
use Application\Form\Groupe\GroupeForm;
use Application\Form\Groupe\GroupeRechercheForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

class GroupeViewHelper extends AbstractEntityActionViewHelper
{
    use HasGroupeTrait;
    use HasAnneeUniversitaireTrait;
    /**
     * @param Groupe|null $groupe
     * @return self
     */
    public function __invoke(Groupe $groupe = null): static
    {
        $this->groupe = $groupe;
        return $this;
    }


    /***********************************
     * Templates, Partial et Fragments *
     **********************************/
    function getNamespace(): string
    {
        return 'application/groupe/groupe';
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['groupes' => $entities], $params);
        return $this->getView()->render('application/groupe/groupe/listes/liste-groupes', $params);
    }

    /**
     * @param GroupeForm $form
     * @return string
     */
    public function renderForm(Form $form) : string
    {
        $param = ['form' => $form];
        return $this->getView()->render("application/groupe/groupe/forms/form-groupe", $param);
    }

    public function renderRechercheForm(GroupeRechercheForm $form): string
    {
        $param = ['form' => $form];
        return $this->getView()->render("application/groupe/groupe/forms/form-recheche-groupe", $param);
    }


    public function renderInfos(): string
    {
        $param = ['groupe' => $this->groupe];
        return $this->getView()->render("application/groupe/groupe/partial/groupe-infos", $param);
    }
    /**
     * @return string
     */
    public function renderListeEtudiants(): string
    {
        $param = ['groupe' => $this->groupe];
        return $this->getView()->render("application/groupe/groupe/listes/liste-etudiants", $param);
    }

    /**
     * @return string
     */
    public function renderListeSessionsStages(): string
    {
        $param = ['groupe' => $this->groupe];
        return $this->getView()->render("application/groupe/groupe/listes/liste-sessions-stages", $param);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasGroupe()){$ressources->add(Groupe::RESOURCE_ID, $this->getGroupe());}
        if($this->hasAnneeUniversitaire()){$ressources->add(AnneeUniversitaire::RESOURCE_ID, $this->getAnneeUniversitaire());}
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->hasGroupe() && $this->callAssertion($ressources, EtudiantPrivileges::GROUPE_AFFICHER),
            Controller::ACTION_AJOUTER => $this->hasAnneeUniversitaire() && $this->callAssertion($ressources, EtudiantPrivileges::GROUPE_AJOUTER),
            Controller::ACTION_AJOUTER_ETUDIANTS, Controller::ACTION_RETIRER_ETUDIANTS
                => $this->hasGroupe() && $this->callAssertion($ressources, EtudiantPrivileges::GROUPE_ADMINISTRER_ETUDIANTS),
            Controller::ACTION_MODIFIER => $this->hasGroupe() && $this->callAssertion($ressources, EtudiantPrivileges::GROUPE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasGroupe() && $this->callAssertion($ressources, EtudiantPrivileges::GROUPE_SUPPRIMER),
            default => false,
        };
    }

    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        $libelle = ($libelle) ?? sprintf("%s", $this->getGroupe()->getLibelle());
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {return $libelle;}
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['groupe' => $this->getGroupe()->getId()], [], true);
        $attributes['title'] = ($attributes['title']) ??  "Afficher le groupe";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienGroupePrecedent() : string
    {
        if(!$this->hasGroupe() || $this->getGroupe()->getGroupePrecedent() === null){return "";}
        $groupe = $this->getGroupe()->getGroupePrecedent();
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['groupe' => $groupe->getId()], [], true);
        $libelle= Icone::PRECEDENT;
        $attributes['class'] = "btn btn-secondary btn-sm m-1";
        $attributes['title'] = "Fiche du groupe précédent";
        return $this->generateActionLink($url, $libelle, $attributes);
    }


    public function lienGroupeSuivant() : string
    {
        if(!$this->hasGroupe() || $this->getGroupe()->getGroupeSuivant() === null){return "";}
        $groupe = $this->getGroupe()->getGroupeSuivant();
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['groupe' => $groupe->getId()], [], true);
        $libelle= Icone::SUIVANT;
        $attributes['class'] = "btn btn-secondary btn-sm m-1";
        $attributes['title'] = "Fiche du groupe suivant";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, ['anneeUniversitaire' => $this->getAnneeUniversitaire()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un groupe";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_AJOUTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['groupe' => $this->getGroupe()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le groupe";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['groupe' => $this->getGroupe()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le groupe";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouterEtudiants(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER_ETUDIANTS)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER_ETUDIANTS, ['groupe' => $this->getGroupe()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, "Ajouter des étudiants");
        $attributes['title'] = ($attributes['title']) ??  "Ajouter des étudiants";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienRetirerEtudiants(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_RETIRER_ETUDIANTS)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_RETIRER_ETUDIANTS, ['groupe' => $this->getGroupe()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::RETIRER, "Retirer des étudiants");
        $attributes['title'] = ($attributes['title']) ??  "Retirer des étudiants";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger";
        return $this->generateActionLink($url, $libelle, $attributes);
    }


}