<?php


namespace Application\View\Helper\Terrains;

use Application\Controller\Terrain\CategorieStageController as Controller;
use Application\Entity\Db\CategorieStage;
use Application\Entity\Traits\Terrain\HasCategorieStageTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\TerrainPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class CategorieStageViewHelper
 * @package Application\View\Helper\Terrains;
 */
class CategorieStageViewHelper extends AbstractEntityActionViewHelper
{
    use HasCategorieStageTrait;

    /**
     * @param CategorieStage|null $categorieStage
     * @return self
     */
    public function __invoke(CategorieStage $categorieStage = null) : static
    {
        $this->categorieStage = $categorieStage;
        return $this;
    }


    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['categoriesStages' => $entities], $params);
        return $this->getView()->render('application/terrain/categorie-stage/listes/liste-categories-stages', $params);
    }

    function renderForm(Form $form): string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/terrain/categorie-stage/forms/form-categorie-stage', $params);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasCategorieStage()){$ressources->add(CategorieStage::RESOURCE_ID, $this->getCategorieStage());}
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => $this->hasPrivilege(TerrainPrivileges::CATEGORIE_STAGE_AFFICHER),
            Controller::ACTION_AJOUTER, Controller::ACTION_IMPORTER => $this->hasPrivilege(TerrainPrivileges::CATEGORIE_STAGE_AJOUTER),
            Controller::ACTION_AFFICHER => $this->hasCategorieStage() && $this->hasPrivilege(TerrainPrivileges::CATEGORIE_STAGE_AFFICHER),
            Controller::ACTION_MODIFIER => $this->hasCategorieStage() && $this->hasPrivilege(TerrainPrivileges::CATEGORIE_STAGE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasCategorieStage() && $this->callAssertion($ressources, TerrainPrivileges::CATEGORIE_STAGE_SUPPRIMER),
            default => false,
        };
    }

    public function lienLister(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_LISTER)) {
            return "";
        }
        $libelle = ($libelle) ?? Label::render("Liste des catégories", Icone::LISTE);
        $url = $this->getUrl(Controller::ROUTE_INDEX, [], [], true);
        $attributes['title'] = ($attributes['title']) ??  "Listes des catégories de stages";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-secondary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER,  ['categorieStage' => $this->getCategorieStage()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AFFICHER, Icone::AFFICHER);
        $attributes['title'] = ($attributes['title']) ??"Afficher la catégorie de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une catégorie de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_AJOUTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['categorieStage' => $this->getCategorieStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::render(Icone::MODIFIER), Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la catégorie de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['categorieStage' => $this->getCategorieStage()->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la catégorie de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienImporter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_IMPORTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_IMPORTER, [], [], true);
        $libelle = ($libelle) ?? Label::render(Label::IMPORTER, Icone::IMPORTER);
        $attributes['title'] = ($attributes['title']) ?? "Importer des catégories de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_IMPORTER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}