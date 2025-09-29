<?php


namespace Application\View\Helper\Annees;

use Application\Controller\AnneeUniversitaire\AnneeUniversitaireController as Controller;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\SessionStage;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Form\Annees\AnneeUniversitaireForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\AnneePrivileges;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class AnneeUniversitaireViewHelper
 * @package Application\View\Helper\Annees
 */
class AnneeUniversitaireViewHelper extends AbstractEntityActionViewHelper
{
    use AnneeUniversitaireServiceAwareTrait;
    use HasAnneeUniversitaireTrait;

    /**
     * @param \Application\Entity\Db\AnneeUniversitaire|null $annee
     * @return self
     */
    public function __invoke(?AnneeUniversitaire $annee = null): static
    {
        $this->anneeUniversitaire = $annee;
        return $this;
    }
    /**
     * TODO
     * @param AnneeUniversitaireForm $form
     * @return string
     */
    public function renderForm(Form $form) : string
    {
        $param = ['form' => $form];
        return $this->getView()->render('application/annee-universitaire/annee-universitaire/forms/form-annee-universitaire', $param);
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['annees' => $entities], $params);
        return $this->getView()->render('application/annee-universitaire/annee-universitaire/listes/liste-annees-universitaires', $params);
    }

    public function renderInfos() : string
    {
        $params = ['annee' => $this->getAnneeUniversitaire()];
        return $this->getView()->render('application/annee-universitaire/annee-universitaire/partial/annee-infos', $params);
    }

    public function renderCalendrier() : string
    {
        $params = ['annee' => $this->getAnneeUniversitaire()];
        return $this->getView()->render('application/annee-universitaire/annee-universitaire/partial/annee-calendrier', $params);
    }

    public function renderSessionInfos(SessionStage $session) : string
    {
        $params = ['session' => $session];
        return $this->getView()->render('application/annee-universitaire/annee-universitaire/partial/session-stage-infos', $params);
    }
    /**************************
     * Liens pour les actions *
     **************************/
    /**
     * @param string $action
     * @return bool
     */
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasAnneeUniversitaire()){$ressources->add(AnneeUniversitaire::RESOURCE_ID, $this->getAnneeUniversitaire());}
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->hasAnneeUniversitaire() && $this->hasPrivilege(AnneePrivileges::ANNEE_UNIVERSITAIRE_AFFICHER),
            Controller::ACTION_AJOUTER => $this->hasPrivilege(AnneePrivileges::ANNEE_UNIVERSITAIRE_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasAnneeUniversitaire() && $this->callAssertion($ressources, AnneePrivileges::ANNEE_UNIVERSITAIRE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasAnneeUniversitaire() && $this->callAssertion($ressources, AnneePrivileges::ANNEE_UNIVERSITAIRE_SUPPRIMER),
            Controller::ACTION_VALIDER => $this->hasAnneeUniversitaire() && $this->callAssertion($ressources, AnneePrivileges::ANNEE_UNIVERSITAIRE_VALIDER),
            Controller::ACTION_DEVEROUILLER => $this->hasAnneeUniversitaire() && $this->callAssertion($ressources, AnneePrivileges::ANNEE_UNIVERSITAIRE_DEVERROUILLER),
            default => false,
        };
    }


    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if(!$this->hasAnneeUniversitaire()){return "";}
        $libelle = ($libelle) ?? sprintf("%s", $this->getAnneeUniversitaire()->getLibelle());
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return $libelle;
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['anneeUniversitaire' => $this->getAnneeUniversitaire()->getId()], [], true);

        $attributes['title'] = ($attributes['title']) ??  "Afficher l'année universitaire";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAnneePrecedente() : string
    {
        if(!$this->hasAnneeUniversitaire() || $this->getAnneeUniversitaire()->getAnneePrecedente() === null){return "";}

        $currentAnnee = $this->getAnneeUniversitaire();
        $anneePrecedente = $currentAnnee->getAnneePrecedente();
        $this->anneeUniversitaire = $anneePrecedente;
        $isAllowed = $this->actionAllowed(Controller::ACTION_AFFICHER);
        $this->anneeUniversitaire = $currentAnnee;
        if (!$isAllowed) return "";

        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['anneeUniversitaire' => $anneePrecedente->getId()], [], true);
        $libelle= Icone::render(Icone::PRECEDENT);
        $attributes['class'] = "btn btn-secondary btn-sm m-1";
        $attributes['title'] = "Fiche de l'année précédente";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAnneeSuivante() : string
    {
        if(!$this->hasAnneeUniversitaire() || $this->getAnneeUniversitaire()->getAnneeSuivante() === null){return "";}

        $currentAnnee = $this->getAnneeUniversitaire();
        $anneeSuivante = $currentAnnee->getAnneeSuivante();
        $this->anneeUniversitaire = $anneeSuivante;
        $isAllowed = $this->actionAllowed(Controller::ACTION_AFFICHER);
        $this->anneeUniversitaire = $currentAnnee;
        if (!$isAllowed) return "";

        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['anneeUniversitaire' => $anneeSuivante->getId()], [], true);
        $libelle= Icone::render(Icone::SUIVANT);
        $attributes['class'] = "btn btn-secondary btn-sm m-1";
        $attributes['title'] = "Fiche de l'année suivante";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une année universitaire";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $annee = $this->getAnneeUniversitaire();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['anneeUniversitaire' => $annee->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier l'année universitaire";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['anneeUniversitaire' => $this->getAnneeUniversitaire()->getId()], [], true);
        $libelle = Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer l'année universitaire";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
    /**
     * @see Controller::validerAction()
     * @return string
     */
    public function lienValider(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_VALIDER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_VALIDER, ['anneeUniversitaire' => $this->getAnneeUniversitaire()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::VALIDER, Icone::render(Icone::FA_UNLOCK));
        $attributes['title'] = ($attributes['title']) ??  "Valider l'année universitaire";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_VALIDER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    /**
     * @see Controller::deverouillerAction()
     * @return string
     */
    public function lienDeverouiller(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_DEVEROUILLER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_DEVEROUILLER_ANNEE, ['anneeUniversitaire' => $this->getAnneeUniversitaire()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", "<i class='fas fa-lock-open'></i>",  "Déverrouiller");
        $attributes['title'] = ($attributes['title']) ??  "Déverrouiller l'année universitaire";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_DEVEROUILLER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }
}