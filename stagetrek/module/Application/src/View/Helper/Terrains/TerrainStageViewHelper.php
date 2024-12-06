<?php


namespace Application\View\Helper\Terrains;

use Application\Controller\Terrain\TerrainStageController as Controller;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Application\Form\TerrainStage\TerrainStageForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\TerrainPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class TerrainStageViewHelper
 * @package Application\View\Helper\Terrains;
 */
class TerrainStageViewHelper extends AbstractEntityActionViewHelper
{
    use HasTerrainStageTrait;

    /**
     * @param TerrainStage|null $terrainStage
     * @return self
     */
    public function __invoke(TerrainStage $terrainStage = null) : TerrainStageViewHelper
    {
        $this->terrainStage = $terrainStage;
        return $this;
    }

    /**
     * @param TerrainStageForm $form
     * @return string
     */
    public function renderForm(Form $form) : string
    {
        $params = ['form' => $form];
        return $this->getView()->render('application/terrain/terrain-stage/forms/form-terrain-stage', $params);
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['terrainsStages' => $entities], $params);
        return $this->getView()->render('application/terrain/terrain-stage/listes/liste-terrains-stages', $params);
    }

    public function renderListeTerrainsAssocies() : string
    {
        $params = ['terrainStage' => $this->getTerrainStage()];
        return $this->getView()->render('application/terrain/terrain-stage/listes/liste-terrains-associes', $params);
    }

    public function renderListeContacts() : string
    {
        if(!$this->hasTerrainStage()){return "";}
        $params = ['terrainStage' => $this->getTerrainStage()];
        return $this->getView()->render('application/terrain/terrain-stage/listes/liste-contacts-terrain-stage', $params);
    }

    /**************************
     * Liens pour les actions *
     **************************/
    //TODO : a revoir avec les assertions pour le isAllowed
    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasTerrainStage()){$ressources->add(TerrainStage::RESOURCE_ID, $this->getTerrainStage());}
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => $this->hasPrivilege(TerrainPrivileges::TERRAIN_STAGE_AFFICHER),
            Controller::ACTION_AJOUTER, Controller::ACTION_IMPORTER => $this->hasPrivilege(TerrainPrivileges::TERRAIN_STAGE_AJOUTER),
            Controller::ACTION_AFFICHER => $this->hasTerrainStage() && $this->hasPrivilege(TerrainPrivileges::TERRAIN_STAGE_AFFICHER),
            Controller::ACTION_MODIFIER => $this->hasTerrainStage() && $this->hasPrivilege(TerrainPrivileges::TERRAIN_STAGE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasTerrainStage() && $this->callAssertion($ressources, TerrainPrivileges::TERRAIN_STAGE_SUPPRIMER),
            default => false,
        };
    }

    public function lienLister(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_LISTER)) {
            return "";
        }
        $libelle = ($libelle) ?? sprintf("%s Liste des terrains", Icone::LISTE);
        $url = $this->getUrl(Controller::ROUTE_INDEX, [], [], true);
        $attributes['title'] = ($attributes['title']) ??  "Listes des terrains de stages";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-secondary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienAfficher(?string $libelle = null, ?array $attributes = []): string
    {
        if(!$this->hasTerrainStage()){
            return "";
        }
        $libelle = ($libelle) ?? sprintf("%s", $this->getTerrainStage()->getCode());
        if (!$this->actionAllowed(Controller::ACTION_AFFICHER)) {
            return $libelle;
        }
        $url = $this->getUrl(Controller::ROUTE_AFFICHER, ['terrainStage' => $this->getTerrainStage()->getId()], [], true);

        $attributes['title'] = ($attributes['title']) ??  "Afficher le terrain de stage";
        $attributes['class'] = ($attributes['class']) ?? "text-primary";
        return $this->generateActionLink($url, $libelle, $attributes);
    }


    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter  un terrain de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success";
        if(!isset($attributes['data-event'])){$attributes['data-event'] = Controller::EVENT_AJOUTER;}
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_MODIFIER,  ['terrainStage' => $this->getTerrainStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le terrain de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['terrainStage' => $this->getTerrainStage()->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le terrain de stage";
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
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::IMPORTER, Label::IMPORTER);
        $attributes['title'] = ($attributes['title']) ?? "Importer des terrains de stage";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_IMPORTER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
    /********************
     * Autres fonctions *
     ********************/
    /**
     * !!! Sans doute a renommer, reproduite l'url du lien fournis dans l'entité, ce n'est pas un lien d'action comme ceux définit précédement
     * @return string
     */
    public function renderTerrainLienInfo() : string
    {
        if(!$this->hasTerrainStage()){return "";}
        $terrain = $this->getTerrainStage();
        if ($terrain->getLien() === null || $terrain->getLien() == "") {
            return "";
        }
        $url = $terrain->getLien();
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        //Note : le // permet de bien précisé de ne pas rester sur le serveur si le protocole n'est pas spécifié
        $param['target'] ='_blank';
        return $this->generateActionLink($url, $terrain->getLien(), $param);
    }
}