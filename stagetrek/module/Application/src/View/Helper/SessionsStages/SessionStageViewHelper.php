<?php


namespace Application\View\Helper\SessionsStages;

use Application\Controller\Stage\SessionStageController as Controller;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Preference;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Db\TerrainStageNiveauDemande;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Stage\HasSessionStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Form\Stages\SessionStageForm;
use Application\Form\Stages\SessionStageRechercheForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\SessionPrivileges;
use Application\Provider\Privilege\StagePrivileges;
use Application\Service\Preference\Traits\PreferenceServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Application\View\Helper\Interfaces\EtudiantActionViewHelperInterface;
use Application\View\Helper\Interfaces\Implementation\EtudiantActionViewHelperTrait;
/**
 * Class SessionStageViewHelper
 * @package Application\View\Helper\SessionsStages
 */
class SessionStageViewHelper extends AbstractEntityActionViewHelper
    implements EtudiantActionViewHelperInterface
{
    use EtudiantActionViewHelperTrait;
    use SessionStageServiceAwareTrait;
    use HasEtudiantTrait;
    use HasSessionStageTrait;
    use HasValidationStageTrait;
    use HasAnneeUniversitaireTrait;
    use HasSessionStageTrait;

    /**
     * @param SessionStage|null $session
     * @return self
     */
    public function __invoke(SessionStage $session = null) : static
    {
        $this->sessionStage = $session;
        return $this;
    }

    /**
     * @param SessionStageForm $form
     * @return string
     */
    public function renderForm($form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/stage/session-stage/forms/form-session-stage', $params);
    }

    public function renderRechercheForm(SessionstageRechercheForm $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/stage/session-stage/forms/form-recherche-session-stage', $params);
    }


    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['sessionsStages' => $entities], $params);
        return $this->getView()->render('application/stage/session-stage/listes/liste-sessions-stages', $params);
    }

    public function renderInfos() : string
    {
        $params = ['sessionStage' => $this->getSessionStage()];
        return $this->getView()->render('application/stage/session-stage/partial/session-infos', $params);
    }

    public function renderDates() : string
    {
        $params = ['sessionStage' => $this->getSessionStage(),
            'vueEtudiante' => $this->vueEtudianteActive()
        ];
        return $this->getView()->render('application/stage/session-stage/partial/session-dates', $params);
    }

    public function renderPlacesTerrains() : string
    {
        $params = ['sessionStage' => $this->getSessionStage()];
        return $this->getView()->render('application/stage/session-stage/partial/session-places-terrains', $params);
    }

    public function renderPreferences() : string
    {
        $params = ['sessionStage' => $this->getSessionStage()];
        return $this->getView()->render('application/stage/session-stage/partial/session-preferences', $params);
    }

    public function renderConventions() : string
    {
        $params = ['sessionStage' => $this->getSessionStage()];
        return $this->getView()->render('application/stage/session-stage/partial/session-conventions', $params);
    }


    public function renderValidationsStages() : string
    {
        $params = ['sessionStage' => $this->getSessionStage()];
        return $this->getView()->render('application/stage/session-stage/partial/session-validations', $params);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasSessionStage()){$ressources->add(SessionStage::RESOURCE_ID, $this->getSessionStage());}
        if($this->hasAnneeUniversitaire()){$ressources->add(AnneeUniversitaire::RESOURCE_ID, $this->getAnneeUniversitaire());}
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->hasSessionStage() && $this->hasPrivilege(SessionPrivileges::SESSION_STAGE_AFFICHER),
            Controller::ACTION_AJOUTER => $this->hasAnneeUniversitaire() && $this->callAssertion($ressources, SessionPrivileges::SESSION_STAGE_AJOUTER),
            Controller::ACTION_MODIFIER,  Controller::ACTION_MODIFIER_PLACES_TERRAINS
                => $this->hasSessionStage() && $this->callAssertion($ressources, SessionPrivileges::SESSION_STAGE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasSessionStage() && $this->callAssertion($ressources, SessionPrivileges::SESSION_STAGE_SUPPRIMER),
            Controller::ACTION_IMPORTER_PLACES_TERRAINS =>  false && $this->hasSessionStage(),
            Controller::ACTION_MODIFIER_ORDRES_AFFECTATIONS => $this->hasSessionStage() && $this->callAssertion($ressources, StagePrivileges::STAGE_MODIFIER),
            default => false,
        };
        //            case Controller::ACTION_MODIFIER_PLACES_TERRAINS :
//                var_dump("TODO");
//                return false;
//                      return isset($session) && !$session->getStages()->isEmpty() && $this->view->isAllowed(StagePrivileges::getResourceId(SessionPrivileges::SESSION_STAGE_MODIFIER));
//            case Controller::ACTION_IMPORTER_PLACES_TERRAINS :
//                var_dump("TODO");
//                return false;
//                return isset($session) && $this->view->isAllowed(StagePrivileges::getResourceId(SessionPrivileges::SESSION_STAGE_MODIFIER));

    }

    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if(!$this->hasSessionStage()){return "";}
        $libelle = ($libelle) ?? sprintf("%s", $this->getSessionStage()->getLibelle());
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return $libelle;
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['sessionStage' => $this->getSessionStage()->getId()], [], true);

        $attributes['title'] = ($attributes['title']) ??  "Afficher la session de stage";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSessionPrecedente() : string
    {
        if(!$this->hasSessionStage() || $this->getSessionStage()->getSessionPrecedente() === null){return "";}
        $session = $this->getSessionStage()->getSessionPrecedente();
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['sessionStage' => $session->getId()], [], true);
        $libelle= Icone::PRECEDENT;
        $attributes['class'] = "btn btn-secondary btn-sm m-1";
        $attributes['title'] = "Fiche de la session precedente";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSessionSuivante() : string
    {
        if(!$this->hasSessionStage() || $this->getSessionStage()->getSessionSuivante() === null){return "";}
        $session = $this->getSessionStage()->getSessionSuivante();
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['sessionStage' => $session->getId()], [], true);
        $libelle= Icone::SUIVANT;
        $attributes['class'] = "btn btn-secondary btn-sm m-1";
        $attributes['title'] = "Fiche de la session suivante";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, ['anneeUniversitaire' => $this->getAnneeUniversitaire()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une session de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_AJOUTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['sessionStage' => $this->getSessionStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la session de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['sessionStage' => $this->getSessionStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la session de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifierPlaces(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER_PLACES_TERRAINS)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER_PLACES_TERRAIN,  ['sessionStage' => $this->getSessionStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, "Modifier le nombre de place(s)");
        $attributes['title'] = ($attributes['title']) ??"Modifier le nombre de place(s) proposée(s)";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifierOrdresAfectations(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER_ORDRES_AFFECTATIONS)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER_ORDRES_AFFECTAIONS,  ['sessionStage' => $this->getSessionStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", "<span class='icon icon-ordre'></span>","Ordres d'affectations");
        $attributes['title'] = ($attributes['title']) ?? "Modifier les ordres d'affectations";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienImporterPlaces(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_IMPORTER_PLACES_TERRAINS)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_IMPORTER_PLACES_TERRAIN,  ['sessionStage' => $this->getSessionStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::IMPORTER,  Label::IMPORTER);
        $attributes['title'] = ($attributes['title']) ??"Importer le nombre de place(s) ouverte(s)";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    use PreferenceServiceAwareTrait;
    //TODO : fonction a revoir, pas terrible
    public function getDemandesTerrainsInfos(TerrainStage $terrain) : string
    {
        $session = $this->getSessionStage();
        if (!isset($session)) {return "";}

        $max = 0;
        $total = 0;
        $demmandes=[];
        /** @var Stage $stage */
        foreach ($session->getStages() as $stage) {
            /** @var Preference $pref */
            foreach ($stage->getPreferences() as $pref) {
                $t =($terrain->isTerrainPrincipal()) ? $pref->getTerrainStage() : $pref->getTerrainStageSecondaire();
                if (!isset($t) || $t->getId() != $terrain->getId()) {
                    continue;
                }
                $rang = $pref->getRang();
                $max = max($max, $rang);
                if (!isset($demmandes[$rang])) {
                    $demmandes[$rang] = 0;
                }
                $demmandes[$rang]++;
                $total++;
            }
        }

        $niveauDemande = $session->getNiveauDemande($terrain);

        $libelleDemande = (isset($niveauDemande)) ? $niveauDemande->getLibelle() : "Indéterminée";
        $code = ($niveauDemande->getCode()) ?? $niveauDemande::INDETERMINE;
        $badgeClass = match ($code) {
                TerrainStageNiveauDemande::INDETERMINE => "badge bnd bnd-na",
                TerrainStageNiveauDemande::FERME => "badge bnd bnd-ferme",
                TerrainStageNiveauDemande::NO_DEMANDE => "badge bnd bnd-0",
                TerrainStageNiveauDemande::RANG_1 => "badge bnd bnd-1",
                TerrainStageNiveauDemande::RANG_2 => "badge bnd bnd-2",
                TerrainStageNiveauDemande::RANG_3 => "badge bnd bnd-3",
                TerrainStageNiveauDemande::RANG_4 => "badge bnd bnd-4",
                TerrainStageNiveauDemande::RANG_5 => "badge bnd bnd-5",
                TerrainStageNiveauDemande::RANG_6 => "badge bnd bnd-6",
                TerrainStageNiveauDemande::RANG_7 => "badge bnd bnd-7",
                TerrainStageNiveauDemande::RANG_8 => "badge bnd bnd-8",
                TerrainStageNiveauDemande::RANG_9 => "badge bnd bnd-9",
                TerrainStageNiveauDemande::RANG_10 => "badge bnd bnd-10",
            };

        $title = "Demandes";
        $libelle = sprintf("<span class='mx-1 %s'>%s</span>", $badgeClass, $libelleDemande);
        $content = "";
        if ($total == 0)
        {
            $content = "<span class='text-muted'>Aucune préférence sur le terrain de stage</span>";
        }
        else{
            for($rang=1; $rang<=$max; $rang++){
                $content .= sprintf("<div class=''>Rang %s : %s</div>",
                    $rang, ($demmandes[$rang]) ?? 0,
                );
            }
            $content .= "<hr class='my-1'/>";
            $content .= "<div> Total : " . $total . "</div>";
        }

        return sprintf('<a class=""
                data-bs-toggle="popover"
                data-bs-placement="bottom"
                data-bs-trigger="focus"
                tabindex="0"
                data-bs-content="%s"
                title="%s"
                >
            %s</a>',
            $content,
            $title,
            $libelle
        );
    }
}