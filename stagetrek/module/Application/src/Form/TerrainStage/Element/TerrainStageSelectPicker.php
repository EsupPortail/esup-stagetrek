<?php


namespace Application\Form\TerrainStage\Element;

use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\TerrainStage;
use Application\Form\Abstrait\Element\AbstractSelectPicker;
use Application\Provider\TypeStage\TypeTerrainStageProvider;
use Exception;

class TerrainStageSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData() : static
    {
        $terrains = $this->getObjectManager()->getRepository(TerrainStage::class)->findAll();
        $terrains = TerrainStage::sort($terrains);
        $this->setTerrainsStages($terrains);
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function useTypeTerrainStageData(string $type) : static
    {
        $filter = match ($type) {
            TypeTerrainStageProvider::TERRAIN_PRINCIPAL => true,
            TypeTerrainStageProvider::TERRAIN_SECONDAIRE => false,
            default => throw new Exception("Type de terrain indéterminé"),
        };
        //TODO : voir comment distinguer les catégorie principale et secondaire
        //Est-ce qu'il existe réelement la notion de catégorie secondaire au final ? pas vraiment sur
        $terrains = $this->getObjectManager()->getRepository(TerrainStage::class)->findBy(['isTerrainPrincipal' => $filter]);
        $terrains = TerrainStage::sort($terrains);
        $this->setTerrainsStages($terrains);
        return $this;
    }

    /**
     * @param TerrainStage[] $terrainsStages
     */
    public function setTerrainsStages(array $terrainsStages) : static
    {
        //!!!Supprime tout les terrains de stages précédents
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = [];
        $this->setOptions($inputOptions);
        foreach ($terrainsStages as $t){
            $this->addTerrainStage($t);
        }
        return $this;
    }
    //Permet nottament de modifier le label de la catégorie
    public function addCategorieStage(CategorieStage $categorieStage) : static
    {
        if($this->hasCategorieStage($categorieStage)) return $this;
        $this->setCategorieStageOption($categorieStage, 'label', $categorieStage->getLibelle());
        $this->setCategorieStageOption($categorieStage, 'options', []);
        return $this;
    }

    public function addTerrainStage(TerrainStage $terrainStage) : static
    {
        if($this->hasTerrainStage($terrainStage)) return $this;
        $categorie = $terrainStage->getCategorieStage();
        if(!$this->hasCategorieStage($categorie)){
            $this->addCategorieStage($categorie);
        }
        $this->setTerrainStageOption($terrainStage, 'label', $terrainStage->getLibelle());
        $this->setTerrainStageOption($terrainStage, 'value', $terrainStage->getId());
        return $this;
    }

    public function removeCategorieDeStage(CategorieStage $categorieStage) : static
    {
        if(!$this->hasCategorieStage($categorieStage))  return $this;
        $value_options = $this->getOption('value_options');
        unset($value_options[$categorieStage->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function removeTerrainStage(TerrainStage $terrain) : static
    {
        if(!$this->hasTerrainStage($terrain)) return $this;
        $categorie = $terrain->getCategorieStage();
        $value_options = $this->getOption('value_options');
        unset($value_options[$categorie->getId()]['options'][$terrain->getId()]);
        if(empty($value_options[$categorie->getId()]['options'])){
            $this->removeCategorieDeStage($categorie);
            //Car a été changé
            $value_options = $this->getOption('value_options');
        }
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }


    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasCategorieStage(CategorieStage $categorieStage) : bool
    {
        $value_options = $this->getOption('value_options');
        return ($value_options && key_exists($categorieStage->getId(), $value_options));
    }
    public function hasTerrainStage(TerrainStage $terrainStage) : bool
    {
        $categorie = $terrainStage->getCategorieStage();
        if(!$this->hasCategorieStage($categorie)) return false;
        $categorieOption = $this->getCategorieStageOptions($categorie);
        return (key_exists('options', $categorieOption) && key_exists($terrainStage->getId(), $categorieOption['options']));
    }

    public function getCategorieStageOptions(CategorieStage $categorieStage) : array{
        if(!$this->hasCategorieStage($categorieStage)) return [];
        $value_options = $this->getOption('value_options');
        return $value_options[$categorieStage->getId()];
    }
    public function getCategorieStageAttributes(CategorieStage $categorieStage) : array
    {
        $categorieOptions = $this->getCategorieStageOptions($categorieStage);
        if(!key_exists('attributes', $categorieOptions)){return [];}
        return $categorieOptions['attributes'];
    }

    public function getTerrainStageOptions(TerrainStage $terrainStage) : array
    {
        if(!$this->hasTerrainStage($terrainStage)) return [];
        $categorie = $terrainStage->getCategorieStage();
        $categorieOptions = $this->getCategorieStageOptions($categorie);
        return  $categorieOptions['options'][$terrainStage->getId()];
    }

    public function getTerrainStageAttributes(TerrainStage $terrainStage) : array
    {
        $terrainOptions = $this->getTerrainStageOptions($terrainStage);
        if(!key_exists('attributes', $terrainOptions)){return [];}
        return $terrainOptions['attributes'];
    }

    /**
     * ie : setCategorieStageOption($terrain, 'label', "Label de la catégorie")
     * @param CategorieStage $categorieStage
     * @param string $key
     * @param mixed $value
     * @return \Application\Form\TerrainStage\Element\TerrainStageSelectPicker
     */
    public function setCategorieStageOption(CategorieStage $categorieStage, string $key, mixed $value) : static
    {
        $options = $this->getCategorieStageOptions($categorieStage);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$categorieStage->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /**
     * ie : setTerrainStageOption($terrain, 'label', "Label du terrain")
     * @param TerrainStage $terrainStage
     * @param String $key
     * @param mixed $value
     * @return \Application\Form\TerrainStage\Element\TerrainStageSelectPicker
     */
    public function setTerrainStageOption(TerrainStage $terrainStage, string $key, mixed $value) : static
    {
        $options = $this->getTerrainStageOptions($terrainStage);
        $options[$key] = $value;
        $categorie = $terrainStage->getCategorieStage();
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$categorie->getId()]['options'][$terrainStage->getId()]=$options;
        $this->setOptions($inputOptions);
        return $this;
    }

    //Permet nottament de rajouter des data-attributes
    public function setCategorieStageAttribute(CategorieStage $catagorieStage, string $key, mixed $value) : static
    {
        if(!$this->hasCategorieStage($catagorieStage)) return $this;
        $attributes = $this->getCategorieStageAttributes($catagorieStage);
        $attributes[$key]=$value;
        $this->setCategorieStageOption($catagorieStage, 'attributes', $attributes);
        return $this;
    }

    public function setTerrainStageAttribute(TerrainStage $terrainStage, string $key, mixed $value) : static
    {
        if(!$this->hasTerrainStage($terrainStage))  return $this;
        $attributes = $this->getTerrainStageAttributes($terrainStage);
        $attributes[$key]=$value;
        $this->setTerrainStageOption($terrainStage, 'attributes', $attributes);
        return $this;
    }
}