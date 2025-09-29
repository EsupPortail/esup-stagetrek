<?php

namespace Application\View\Helper\Stages;

use Application\Controller\Stage\StageController as Controller;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Preference;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Stage\HasSessionStageTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Form\Stages\ValidationStageForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Provider\Privilege\StagePrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Application\View\Helper\Interfaces\EtudiantActionViewHelperInterface;
use Application\View\Helper\Interfaces\Implementation\EtudiantActionViewHelperTrait;
use DateTime;

/**
 * Class StageViewHelper
 * @package Application\View\Helper\Stages
 */
class StageViewHelper extends AbstractEntityActionViewHelper
implements EtudiantActionViewHelperInterface
{
    use EtudiantActionViewHelperTrait;
    use HasValidationStageTrait;
    use HasEtudiantTrait;
    use HasStageTrait;
    use HasSessionStageTrait;

    /**
     * @param Stage|null $stage
     * @return self
     */
    public function __invoke(Stage $stage = null): static
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['stages' => $entities], $params);
        return $this->getView()->render('application/stage/stage/listes/liste-stages', $params);
    }

    /**
     * @return string
     */
    public function renderListeMesStages(): string
    {
        if (!$this->hasEtudiant()) return "";
        $params = ['etudiant' => $this->getEtudiant()];
        return $this->getView()->render('application/stage/stage/listes/liste-mes-stages', $params);
    }

    public function renderInfos(): string
    {
        $params = ['stage' => $this->getStage(), 'vueEtudiante' => $this->vueEtudianteActive()];
        return $this->getView()->render('application/stage/stage/partial/stage-infos', $params);
    }

    public function renderAffectationInfos() : string
    {
        $params = ['stage' => $this->getStage(), 'vueEtudiante' => $this->vueEtudianteActive()];
        return $this->getView()->render('application/stage/stage/partial/affectation-infos', $params);
    }

    public function renderListeContacts() : string
    {
        $params = ['stage' => $this->getStage(), 'vueEtudiante' => $this->vueEtudianteActive()];
        return $this->getView()->render('application/stage/stage/listes/liste-contacts-stage', $params);
    }

    public function renderConvention() : string
    {//TODO : a déplacer
        $params = ['stage' => $this->getStage(), 'vueEtudiante' => $this->vueEtudianteActive()];
        return $this->getView()->render('application/stage/stage/partial/convention', $params);
    }

    /**
     * @param ValidationStageForm $form
     * @return string
     */
    public function renderValidationForm(ValidationStageForm $form) : string
    {
        $params = ['stage' => $this->getStage(), 'form' => $form];
        return $this->getView()->render('application/stage/stage/forms/form-validation-stage', $params);
    }


    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if(!$this->hasStage()){return "";}
        $libelle = ($libelle) ?? sprintf("%s", $this->getStage()->getLibelle());
        $action = ($this->vueEtudianteActive()) ? Controller::ACTION_MON_STAGE : Controller::ACTION_AFFICHER;
        $route =  ($this->vueEtudianteActive()) ? Controller::ROUTE_MON_STAGE : Controller::ROUTE_AFFICHER;
        if (!$this->actionAllowed($action)) {
            return $libelle;
        }
//        var_dump($action);
        $url = $this->getUrl($route, ['stage' => $this->getStage()->getId()], [], true);
        $attributes['title'] = ($attributes['title']) ??  "Afficher le stage";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienStagePrecedent(): string
    {
        $stage = $this->getStage();
        if (!isset($stage)) return "";
        $action = ($this->vueEtudianteActive) ? Controller::ACTION_MON_STAGE : Controller::ACTION_AFFICHER;
        $route =  ($this->vueEtudianteActive) ? Controller::ROUTE_MON_STAGE : Controller::ROUTE_AFFICHER;

        //Recherche du premier stage courant disponible (permet de passer des stages non visible pour l'étudiant)
        $currentStage = $this->getStage();
        $stagePrecedent = $currentStage;
        do {
            //Cas d'un stage secondaire : on prend le stage Principal
            if(isset($stagePrecedent) && $stagePrecedent->isStageSecondaire()){
                $stagePrecedent = $stagePrecedent->getStagePrincipal();
            }
            $stagePrecedent = $stagePrecedent->getStagePrecedent();
            //Cas d'un stage secondaire : on prend le stage Principal
            if(isset($stagePrecedent) && $stagePrecedent->isStageSecondaire()){
                $stagePrecedent = $stagePrecedent->getStagePrincipal();
            }
            $this->stage = $stagePrecedent;
            $isAllowed = $this->actionAllowed($action);
        } while (isset($stagePrecedent) && !$isAllowed);
        $this->stage = $currentStage;
        if (!isset($stagePrecedent) || !$isAllowed) return "";


        $url = $this->getUrl($route, ['stage' => $stagePrecedent->getId()], [], true);
        $libelle= Icone::render(Icone::PRECEDENT);
        $attributes['class'] = "btn btn-secondary btn-sm m-1";
        $attributes['title'] = "Fiche du stage précédent";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienStageSuivant(): string
    {
        $stage = $this->getStage();
        if (!isset($stage)) return "";
        $action = ($this->vueEtudianteActive) ? Controller::ACTION_MON_STAGE : Controller::ACTION_AFFICHER;
        $route =  ($this->vueEtudianteActive) ? Controller::ROUTE_MON_STAGE : Controller::ROUTE_AFFICHER;

        $stage = $this->getStage();
        if (!isset($stage)) return "";
        $currentStage = $this->getStage();
        $stageSuivant = $currentStage;
        do {
            $stageSuivant = $stageSuivant->getStageSuivant();
            if(isset($stageSuivant) && $stageSuivant->isStageSecondaire()){
                $stageSuivant = $stageSuivant->getStageSuivant();
            }
            $this->stage = $stageSuivant;
            $isAllowed = $this->actionAllowed($action);
        } while (isset($stageSuivant) && !$isAllowed);
        $this->stage = $currentStage;
        if (!isset($stageSuivant) || !$isAllowed) return "";

        $url = $this->getUrl($route, ['stage' => $stageSuivant->getId()], [], true);
        $libelle= Icone::render(Icone::SUIVANT);
        $attributes['class'] = "btn btn-secondary btn-sm m-1";
        $attributes['title'] = "Fiche du stage suivant";
        return $this->generateActionLink($url, $libelle, $attributes);
    }


    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action): bool
    {

        $ressources = new ArrayRessource();
        if($this->hasStage()){$ressources->add(Stage::RESOURCE_ID, $this->getStage());}
        if($this->hasSessionStage()){$ressources->add(SessionStage::RESOURCE_ID, $this->getSessionStage());}

        switch ($action) {
            case Controller::ACTION_AFFICHER:
            case Controller::ACTION_AFFICHER_INFOS:
            case Controller::ACTION_AFFICHER_AFFECTATION:
            case Controller::ACTION_AFFICHER_CONVENTION:
            case Controller::ACTION_LISTER_CONTACTS:
                return $this->hasStage() && $this->hasPrivilege(StagePrivileges::STAGE_AFFICHER);
            case Controller::ACTION_AJOUTER_STAGES:
                return $this->hasSessionStage() && $this->callAssertion($ressources, StagePrivileges::STAGE_AJOUTER);
            case Controller::ACTION_SUPPRIMER_STAGES:
                return $this->hasSessionStage() && $this->callAssertion($ressources, StagePrivileges::STAGE_SUPPRIMER);
            case Controller::ACTION_MON_STAGE:
                return $this->hasStage() && $this->callAssertion(new ArrayRessource([Stage::RESOURCE_ID => $this->getStage()]), StagePrivileges::ETUDIANT_OWN_STAGES_AFFICHER);
            case Controller::ACTION_MES_STAGES:
                return $this->hasSessionStage() && $this->callAssertion(new ArrayRessource([SessionStage::RESOURCE_ID => $this->getSessionStage()]), StagePrivileges::ETUDIANT_OWN_STAGES_AFFICHER);
            default :
                return false;
        }
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER_STAGES)) {
            return "";
        }
        $data = ['sessionStage' => $this->getSessionStage()->getId()];
        $url = $this->getUrl(Controller::ROUTE_AJOUTER_STAGES, $data, [], true);
        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter des stages";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success";
        return $this->generateActionLink($url, $libelle, $attributes);
    }


    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER_STAGES)) {
            return "";
        }
        $data = ['sessionStage' => $this->getSessionStage()->getId()];
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER_STAGES, $data, [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::render(Icone::SUPPRIMER), "Supprimer des stages");
        $attributes['title'] = ($attributes['title']) ??  "Supprimer des stages";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    /********************
     * Autres fonctions *
     ********************/

    public function getStageEtatValidationIcon(?Stage $stage): string
    {
        if (!isset($stage) || $stage->hasEtatNonEffectue() || $stage->hasEtatEnDisponibilite()) {
            return "<span class='text-muted'>-</span>";
        }
//        Pas d'affectation ou affectation non validé ==> pas de validation
        $affectation = $stage->getAffectationStage();
        if (!isset($affectation) || !($affectation->hasEtatValidee())) {
            return "<span class='text-muted'>-</span>";
        }
        $validation = $stage->getValidationStage();
        $title = "<strong>Validation du stage</strong>";
        $today = new DateTime();
        switch (true) {
            case !isset($validation) :
                $icone = "<span class='text-danger icon icon-warning'></span>";
                $content = "<div>La validation du stage ne peux pas être déterminée</div>";
                break;
            case  ($validation->validationEffectue() && $validation->isValide() && $validation->getWarning()) :
                $icone = "<span class='text-primary icon icon-warning'></span>";
                $content = "<div>Stage validé</div>";
                $content .= "<div>Le responsable de stage a cependant signalé un problème lors du stage.</div>";
                break;
            case  ($validation->validationEffectue() && $validation->isValide()) :
                $icone = "<span class='text-success fas fa-check-circle'></span>";
                $content = "<div>Stage validé</div>";
                break;
            case  ($validation->validationEffectue() && $validation->isInvalide()) :
                $icone = "<span class='text-danger fas fa-times-circle'></span>";
                $content = "<div>Stage non validé</div>";
                break;
            default :
                $content = "<div>Validation non effecutée</div>";
                $contacts = $stage->getContactsStages();
                $canValider = false;
                //Cas différent si l'on est avant ou après la phase de validation
                // Le token de validation n'étant pas requis avant le début de la phase de validation
                /** @var ContactStage $c */
                foreach ($contacts as $c) {
                    if ($c->canValiderStage() && ($today < $stage->getDateDebutValidation() || $c->tokenValide())) {
                        $canValider = true;
                        break;
                    }
                }
                if (!$canValider && $stage->getDateFinCommission() < $today) {
                    $content .= "<div class='text-danger'><strong>Aucun contact du stage ne peut proceder à la validation</strong></div>";
                }
                switch (true) {
                    case  ($stage->getDateFinValidation() < $today) :
                        $icone = "<span class='text-warning fas fa-exclamation-triangle'></span>";
                        $content .= sprintf("<div class='text-muted text-small'>La phase de validation est terminée depuis le %s</div>", $stage->getDateFinValidation()->format('d/m/y'));
                        break;
                    case  ($stage->getDateDebutValidation() < $today) :
                        $icone = "<span class='text-muted fas fa-spinner'></span>";
                        $content .= "<div class='text-muted text-small'>La phase de validation est en cours</div>";
                        break;
                    default :
                        $icone = "<span class='text-muted fas fa-times-circle'></span>";
                        $content .= sprintf("<div class='text-muted text-small'>La phase de validation commence le %s</div>", $stage->getDateDebutValidation()->format('d/m/y'));
                        break;
                }
                if (!$canValider && $stage->getDateDebutStage() < $today) {
                    $icone = "<span class='text-danger fas fa-exclamation-triangle'></span>";
                }
        }
        return sprintf('<a class="" 
            data-bs-toggle="popover" 
            data-bs-placement="bottom" 
            data-bs-trigger="focus"
            tabindex="0"
            data-bs-content="%s"
            title="%s"
            >
        %s</a>', $content, $title, $icone);
    }

    public function getStageEtatValidationOrder(?Stage $stage): int
    {
        if (!isset($stage)) {
            return -4;
        }
        if ($stage->hasEtatNonEffectue()) {
            return -3;
        }
        if ($stage->hasEtatEnDisponibilite()) {
            return -2;
        }
        $affectation = $stage->getAffectationStage();
        if (!isset($affectation) || !($affectation->hasEtatValidee())) {
            return -1;
        }
        $validation = $stage->getValidationStage();
        if (!isset($validation)) { // la validation n'existe pas encore = cas d'erreur
            return -4;
        }
        if ($validation->isValide()) {
            return 3;
        }
        if ($validation->isInvalide()) {
            return 2;
        }
        $today = new DateTime();
        $contacts = $stage->getContactsStages();
        $canValider = false;
        /** @var ContactStage $c */
        foreach ($contacts as $c) {
            if ($c->canValiderStage() && ($today < $stage->getDateDebutValidation() || $c->tokenValide())) {
                $canValider = true;
            }
        }
        if (!$canValider && $stage->getDateDebutStage() < $today) {
            return 5;
        }
        if ($stage->getDateFinValidation() < $today) {
            return 4;
        }
        if ($stage->getDateDebutValidation() < $today) {
            return 1;
        }
        return 0;
    }
}