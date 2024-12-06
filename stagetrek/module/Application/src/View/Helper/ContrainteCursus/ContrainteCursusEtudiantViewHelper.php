<?php


namespace Application\View\Helper\ContrainteCursus;

use Application\Controller\Contrainte\ContrainteCursusEtudiantController as Controller;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\Etudiant;
use Application\Entity\Traits\Contraintes\HasContrainteCursusEtudiantTrait;
use Application\Entity\Traits\Contraintes\HasContrainteCursusTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Form\Contrainte\ContrainteCursusEtudiantForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use UnicaenApp\View\Helper\Messenger;

/**
 * Class ContrainteCursusEtudiantViewHelper
 * @package Application\View\Helper\ContrainteCursusEtudiant\ContrainteCursusEtudiantViewHelper
 */
class ContrainteCursusEtudiantViewHelper extends AbstractEntityActionViewHelper
{
    use HasEtudiantTrait;
    use HasContrainteCursusTrait;
    use HasContrainteCursusEtudiantTrait;

    /**
     * @param ContrainteCursusEtudiant|null $contrainte
     * @return self
     */
    public function __invoke(ContrainteCursusEtudiant $contrainte = null): static
    {
        $this->setContrainteCursusEtudiant($contrainte);
        return $this;
    }

    /**
     * @param ContrainteCursusEtudiantForm $form
     * @return string
     */
    public function renderForm($form) : string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/contrainte/contrainte-cursus-etudiant/forms/form-contrainte-cursus-etudiant', $params);
    }


    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['contraintesCursusEtudiant' => $entities], $params);
        return $this->getView()->render('application/contrainte/contrainte-cursus-etudiant/listes/liste-contraintes-cursus-etudiant', $params);
    }
    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {

        $ressource = new ArrayRessource();
        if($this->hasContrainteCursusEtudiant()){
            $ressource->add(ContrainteCursusEtudiant::RESOURCE_ID, $this->getContrainteCursusEtudiant());
        }
        if($this->hasEtudiant()){
            $ressource->add(Etudiant::RESOURCE_ID, $this->getEtudiant());
        }
        return  match ($action) {
            Controller::ACTION_LISTER => $this->hasEtudiant() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_CONTRAINTES_AFFICHER),
            Controller::ACTION_AFFICHER => $this->hasContrainteCursusEtudiant() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_CONTRAINTES_AFFICHER),
            Controller::ACTION_MODIFIER => $this->hasContrainteCursusEtudiant() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_CONTRAINTE_MODIFIER),
            Controller::ACTION_ACTIVER => $this->hasContrainteCursusEtudiant() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_CONTRAINTE_ACTIVER),
            Controller::ACTION_DESACTIVER => $this->hasContrainteCursusEtudiant() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_CONTRAINTE_DESACTIVER),
            Controller::ACTION_VALIDER => $this->hasContrainteCursusEtudiant() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_CONTRAINTE_VALIDER),
            Controller::ACTION_INVALIDER => $this->hasContrainteCursusEtudiant() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_CONTRAINTE_INVALIDER),
            default => false,
        };
    }


    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if(!$this->hasContrainteCursusEtudiant()){return "";}
        $libelle = ($libelle) ?? sprintf("%s", $this->getContrainteCursusEtudiant()->getLibelle());

        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return $libelle;
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['contrainteCursusEtudiant' => $this->getContrainteCursusEtudiant()->getId()], [], true);
        $attributes['title'] = ($attributes['title']) ??  "Afficher la contrainte";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['contrainteCursusEtudiant' =>  $this->getContrainteCursusEtudiant()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la contrainte pour l'étudiant";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienActiver(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_ACTIVER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_ACTIVER, ['contrainteCursusEtudiant' =>  $this->getContrainteCursusEtudiant()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s Activer", Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??"Activer la contrainte pour l'étudiant";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }


    public function lienDesactiver(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_DESACTIVER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_DESACTIVER, ['contrainteCursusEtudiant' =>  $this->getContrainteCursusEtudiant()->getId()], [], true);
        $libelle = ($libelle) ??"<span class='fas fa-times-circle'></span> Désactivé";
        $attributes['title'] = ($attributes['title']) ??"Désactiver la contrainte pour l'étudiant";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-muted ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienValider(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_VALIDER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_VALIDER, ['contrainteCursusEtudiant' =>  $this->getContrainteCursusEtudiant()->getId()], [], true);
        $libelle = ($libelle) ??"<span class='icon icon-succes'></span> Valider";
        $attributes['title'] = ($attributes['title']) ??"Valider la contrainte pour l'étudiant";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienInvalider(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_INVALIDER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_INVALIDER, ['contrainteCursusEtudiant' =>  $this->getContrainteCursusEtudiant()->getId()], [], true);
        $libelle = ($libelle) ??"<span class='icon icon-delete'></span> Invalider";
        $attributes['title'] = ($attributes['title']) ??"Invalider la contrainte pour l'étudiant";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }


    /********************
     * Autres fonctions *
     ********************/
    public function renderValidationInfo() : string
    {
        $contrainte = $this->getContrainteCursusEtudiant();
        if(!$contrainte->isActive()){return "";}
        $nb = (($contrainte->getNbStagesValidant()) ?? 0) + (($contrainte->getNbEquivalences()) ?? 0);
        $dataContent = sprintf("Nombre de stage(s) validé(s) : %s<br/>+/- Equivalence(s) accordée(s) : %s",
            $contrainte->getNbStagesValidant(),
            $contrainte->getNbEquivalences()
        );
        $reste = $contrainte->getResteASatisfaire();
        if($reste>0){
            $dataContent .= sprintf('<hr />Reste à satisfaire : %s', $reste);
        }
        return sprintf('%s <a
                    data-bs-toggle="popover" 
                    data-bs-placement="bottom" 
                    data-bs-trigger="focus"
                    tabindex="0"
                    data-bs-content="%s"
                    ><span class="icon icon-info text-primary"></span></a>',
                $nb, $dataContent
        );
    }
}