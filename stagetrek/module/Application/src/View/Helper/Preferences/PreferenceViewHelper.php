<?php


namespace Application\View\Helper\Preferences;

use Application\Controller\Preference\PreferenceController as Controller;
use Application\Entity\Db\Preference;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Traits\Preference\HasPreferenceTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\DegreDemande\TerrainStageNiveauDemandeProvider;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Parametre\ParametreProvider;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Service\Contrainte\Traits\ContrainteCursusEtudiantServiceAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Application\View\Helper\Interfaces\EtudiantActionViewHelperInterface;
use Application\View\Helper\Interfaces\Implementation\EtudiantActionViewHelperTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Laminas\Form\Form;

/**
 * Class PreferenceViewHelper
 * @package Application\View\Helper\Etudiant
 */
class PreferenceViewHelper  extends AbstractEntityActionViewHelper
    implements EtudiantActionViewHelperInterface
{
    use EtudiantActionViewHelperTrait;
    use HasPreferenceTrait;
    use HasStageTrait;
    use HasValidationStageTrait;

    /**
     * @param Preference|null $preference
     * @return self
     */
    public function __invoke(Preference $preference = null): static
    {
        $this->preference = $preference;
        return $this;
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['preferences' => $entities, 'stage'=>$this->getStage(),
            'vueEtudiante' => $this->vueEtudianteActive()
        ], $params);
        return $this->getView()->render('application/preference/preference/listes/liste-preferences', $params);
    }

    function renderForm(Form $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/preference/preference/forms/form-preference', $params);
    }

    public function renderListePlaces() : string
    {
        $params = ['stage' => $this->getStage()];
        return $this->getView()->render('application/preference/preference/listes/liste-places', $params);
    }

    use ContrainteCursusEtudiantServiceAwareTrait;

    public function renderRecommandations() : string
    {
        $stage = $this->stage;
        try {
            $etudiant = $this->getContrainteCursusEtudiantService()->computeRecommandationsContraintes($stage->getEtudiant());
        } catch (Exception | NotSupported) {
            return "";
        }
        $stage->setEtudiant($etudiant);
        $params = ['stage' => $this->getStage()];
        return $this->getView()->render('application/preference/preference/partial/preference-recommandations', $params);
    }


    public function actionAllowed(string $action) : bool
    {
        $ressource = new ArrayRessource();
        if($this->hasStage()){$ressource->add(Stage::RESOURCE_ID, $this->getStage());}
        if($this->hasPreference()){$ressource->add(Preference::RESOURCE_ID, $this->getPreference());}

        return match (true) {
            $action == Controller::ACTION_LISTER && $this->vueEtudianteActive(),
            $action == Controller::ACTION_LISTER_PLACES && $this->vueEtudianteActive() =>
                $this->hasStage() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_AFFICHER),
            $action == Controller::ACTION_LISTER,
            $action == Controller::ACTION_LISTER_PLACES
                => $this->hasStage() && $this->callAssertion($ressource, EtudiantPrivileges::PREFERENCE_AFFICHER),
            $action == Controller::ACTION_AJOUTER && $this->vueEtudianteActive() => $this->hasStage() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_AJOUTER),
            $action == Controller::ACTION_AJOUTER => $this->hasStage() && $this->callAssertion($ressource, EtudiantPrivileges::PREFERENCE_AJOUTER),
            $action == Controller::ACTION_MODIFIER && $this->vueEtudianteActive(),
            $action == Controller::ACTION_MODIFIER_RANG && $this->vueEtudianteActive(),
                => $this->hasPreference() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_MODIFIER),
            $action == Controller::ACTION_MODIFIER,
            $action == Controller::ACTION_MODIFIER_RANG,
                => $this->hasPreference() && $this->callAssertion($ressource, EtudiantPrivileges::PREFERENCE_MODIFIER),
            $action == Controller::ACTION_MODIFIER_PREFERENCES && $this->vueEtudianteActive(),
                => $this->hasStage() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_MODIFIER),
            $action == Controller::ACTION_MODIFIER_PREFERENCES,
                => $this->hasStage() && $this->callAssertion($ressource, EtudiantPrivileges::PREFERENCE_MODIFIER),
            $action == Controller::ACTION_SUPPRIMER && $this->vueEtudianteActive()
                => $this->hasStage() && $this->callAssertion($ressource, EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_SUPPRIMER),
            $action == Controller::ACTION_SUPPRIMER,
                => $this->hasStage() && $this->callAssertion($ressource, EtudiantPrivileges::PREFERENCE_SUPPRIMER),

            default => false,
        };
    }

    public function lienModifierPreferences(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER_PREFERENCES)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER_PREFERENCES,  ['stage' => $this->getStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier les préférences du stages";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }

        $url = $this->getUrl(Controller::ROUTE_AJOUTER, ['stage' => $this->getStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une préférence";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_AJOUTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['preference' => $this->getPreference()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la préférence";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['preference' => $this->getPreference()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la préférence";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    /********************
     * Autres fonctions *
     ********************/
    public function renderDemande(TerrainStage $terrain = null) : string
    {
        $preference = $this->getPreference();
        $stage = $this->getStage();
        $terrain = ($terrain) ?? $preference->getTerrainStage();
        $session = ($preference) ? ($preference->getSessionStage()) : ($stage->getSessionStage());

        $niveauDemande = $session->getNiveauDemande($terrain);

        $libelleDemande = (isset($niveauDemande)) ? $niveauDemande->getLibelle() : "Indéterminée";
        $badgeClass = (! isset($niveauDemande)) ? "text-muted text-small" :
            match ($niveauDemande->getCode()) {
                TerrainStageNiveauDemandeProvider::INDETERMINE, "text-muted text-small",
                TerrainStageNiveauDemandeProvider::FERME => "badge badge-muted",
                TerrainStageNiveauDemandeProvider::NO_DEMANDE => "badge badge-success",
                TerrainStageNiveauDemandeProvider::RANG_5 => "badge badge-light-success",
                TerrainStageNiveauDemandeProvider::RANG_4 => "badge badge-light-primary",
                TerrainStageNiveauDemandeProvider::RANG_3 => "badge badge-primary",
                TerrainStageNiveauDemandeProvider::RANG_2 => "badge badge-warning",
                TerrainStageNiveauDemandeProvider::RANG_1 => "badge badge-danger"
        };

        $libelle = sprintf("<span class='mx-1 %s'>%s</span>", $badgeClass, $libelleDemande);
        $html = $libelle;

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
            $title = "Demandes";
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

            $html = sprintf('<a class="pointer-hover"
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
        return $html;
    }

    use ParametreServiceAwareTrait;
    public function getRangMaxPreference() : int
    {
        try {
            return $this->getParametreService()->getParametreValue(ParametreProvider::NB_PREFERENCES);
        } catch (NotSupported) {
            return 0;
        }
    }
}