<?php


namespace Application\View\Helper\Convention;

use Application\Controller\Convention\ModeleConventionController as Controller;
use Application\Entity\Db\FaqCategorieQuestion;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Traits\Convention\HasConventionStageTrait;
use Application\Form\Convention\ModeleConventionStageForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ConventionsPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

class ModeleConventionViewHelper extends AbstractEntityActionViewHelper
{
    use HasConventionStageTrait;

    /**
     * @param ModeleConventionStage $modele
     * @return self
     */
    public function __invoke($modele = null)
    {
        $this->modeleConventionStage = $modele;
        return $this;
    }



    function renderForm(Form $form): string
    {
        $params=['form' => $form];
        return $this->getView()->render('application/convention/modele-convention/forms/form-modele-convention', $params);
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['modelesConventionsStages' => $entities], $params);
        return $this->getView()->render('application/convention/modele-convention/listes/liste-modeles-conventions', $params);
    }



    public function renderInfos()
    {
        $params = ['modeleConventionStage' => $this->modeleConventionStage];
        return $this->getView()->render('application/convention/modele-convention/partial/modele-infos', $params);
    }


    public function renderListeTerrains()
    {
        $params = ['modeleConventionStage' => $this->modeleConventionStage];
        return $this->getView()->render('application/convention/modele-convention/listes/liste-terrains', $params);

    }

    /**************************
     * Liens pour les actions *
     **************************/
    function actionAllowed(string $action): bool
    {
        $ressources = new ArrayRessource();
        if($this->hasModeleConventionStage()){
            $ressources->add(ModeleConventionStage::RESOURCE_ID, $this->getModeleConventionStage());
        }
        switch ($action) {
            case Controller::ACTION_AFFICHER:
                return $this->hasModeleConventionStage() && $this->hasPrivilege(ConventionsPrivileges::MODELE_CONVENTION_AFFICHER);
            case Controller::ACTION_LISTER:
                return $this->hasPrivilege(ConventionsPrivileges::MODELE_CONVENTION_AFFICHER);
            case Controller::ACTION_AJOUTER:
                return $this->hasPrivilege(ConventionsPrivileges::MODELE_CONVENTION_AJOUTER);
            case Controller::ACTION_MODIFIER:
                return $this->hasModeleConventionStage() && $this->hasPrivilege(ConventionsPrivileges::MODELE_CONVENTION_MODIFIER);
            case Controller::ACTION_SUPPRIMER:
                return $this->hasModeleConventionStage() && $this->callAssertion($ressources, ConventionsPrivileges::MODELE_CONVENTION_SUPPRIMER);
            default :
                return false;
        }
    }
    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if(!$this->hasModeleConventionStage()){
            return "";
        }
        $libelle = ($libelle) ?? sprintf("%s", $this->getModeleConventionStage()->getCode());
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return $libelle;
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['modeleConventionStage' => $this->getModeleConventionStage()->getId()], [], true);

        $attributes['title'] = ($attributes['title']) ??  "Voir le modéle de convention de stage";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un modéle de convention";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_AJOUTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['modeleConventionStage' => $this->getModeleConventionStage()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le modéle de convention";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['modeleConventionStage' => $this->getModeleConventionStage()->getId()], [], true);
        $libelle = Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le modéle de convention de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}