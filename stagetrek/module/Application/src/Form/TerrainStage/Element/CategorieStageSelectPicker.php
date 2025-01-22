<?php


namespace Application\Form\TerrainStage\Element;

use Application\Entity\Db\CategorieStage;
use Application\Form\Abstrait\Element\AbstractSelectPicker;
use Exception;

class CategorieStageSelectPicker extends AbstractSelectPicker
{
    public function setDefaultData() : static
    {
        //TODO : voir comment distinguer les catégorie principale et secondaire
        //Est-ce qu'il existe réelement la notion de catégorie secondaire au final ? pas vraiment sur
        $categories = $this->getObjectManager()->getRepository(CategorieStage::class)->findAll();
        $categories = CategorieStage::sort($categories);
        $this->setCategoriesStages($categories);
        /** @var CategorieStage $c */
        foreach ($categories as $c){
            $this->setCategorieStageAttribute($c, 'data-type-categorie', ($c->isCategoriePrincipale()) ?
                CategorieStage::TYPE_CATEGORIE_PRINCIPALE :  CategorieStage::TYPE_CATEGORIE_SECONDAIRE
            );
        }
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function useTypeCategorieData(string $type) : static
    {
        $filter = match ($type) {
            CategorieStage::TYPE_CATEGORIE_PRINCIPALE => true,
            CategorieStage::TYPE_CATEGORIE_SECONDAIRE => false,
            default => throw new Exception("Type de catégorie de terrain indéterminée"),
        };
        //TODO : voir comment distinguer les catégorie principale et secondaire
        //Est-ce qu'il existe réelement la notion de catégorie secondaire au final ? pas vraiment sur
        $categories = $this->getObjectManager()->getRepository(CategorieStage::class)->findBy(['categoriePrincipale' => $filter]);
        $categories = CategorieStage::sort($categories);
        $this->setCategoriesStages($categories);
        return $this;
    }

    public function setCategoriesStages(array $categories) : static
    {
        //!!!Supprime toutes les catégories de stages précédentes
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = [];
        $this->setOptions($inputOptions);
        foreach ($categories as $c){
            $this->addCategorieStage($c);
        }
        return $this;
    }

    public function addCategorieStage(CategorieStage $categorieStage) : static
    {
        if($this->hasCategorieStage($categorieStage)) return $this;
        $typeCat = self::getTypeCategorieStage($categorieStage);
        if(!$this->hastypeCategorie($typeCat)){
            $this->addTypeCategorie($typeCat);
        }
        $this->setCategorieStageOption($categorieStage, 'label', $categorieStage->getLibelle());
        $this->setCategorieStageOption($categorieStage, 'value', $categorieStage->getId());
        return $this;
    }

    public function removeCategorieStage(CategorieStage $categorieStage) : static
    {
        if(!$this->hasCategorieStage($categorieStage)) return $this;
        $typeCat = self::getTypeCategorieStage($categorieStage);

        $value_options = $this->getOption('value_options');
        unset($value_options[$typeCat]['options'][$categorieStage->getId()]);
        if(empty($value_options[$typeCat]['options'])){
            $this->removeTypeCategorie($typeCat);
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
        $typeCat = self::getTypeCategorieStage($categorieStage);
        if(!$this->hasTypeCategorie($typeCat)) return false;
        $typeOption = $this->getTypeCategorieOptions($typeCat);
        return (isset($typeOption['options'][$categorieStage->getId()]));
    }

    public function getCategorieStageOptions(CategorieStage $categorieStage){
        if(!$this->hasCategorieStage($categorieStage)) return [];
        $typeCat = self::getTypeCategorieStage($categorieStage);
        $typeOptions = $this->getTypeCategorieOptions($typeCat);
        return  $typeOptions['options'][$categorieStage->getId()];
    }

    public function getCategorieStageAttributes(CategorieStage $categorieStage) : array
    {
        $catOptions = $this->getCategorieStageOptions($categorieStage);
        if(!key_exists('attributes', $catOptions)){return [];}
        return $catOptions['attributes'];
    }

    public function setCategorieStageOption($categorieStage, $key, $value) : static
    {
        $options = $this->getCategoriestageOptions($categorieStage);
        $typeCat = self::getTypeCategorieStage($categorieStage);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$typeCat]['options'][$categorieStage->getId()]=$options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setCategorieStageAttribute($categorieStage, $key, $value) : static
    {
        $attributes = $this->getCategorieStageAttributes($categorieStage);
        $attributes[$key]=$value;
        $this->setCategorieStageOption($categorieStage, 'attributes', $attributes);
        return $this;
    }

    //Type de catégorie
    public function hastypeCategorie(string $type) : bool
    {
        $value_options = $this->getOption('value_options');
        return isset($value_options[$type]);
    }

    public function addTypeCategorie(string $type) : static
    {
        if($this->hastypeCategorie($type)) return $this;
        $this->setTypeCategorieOption($type, 'label', $type);
        $this->setTypeCategorieOption($type, 'options', []);
        return  $this;
    }

    public function removeTypeCategorie(string $type) : static
    {
        if(!$this->hasTypeCategorie($type)) return $this;
        $value_options = $this->getOption('value_options');
        unset($value_options[$type]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function getTypeCategorieOptions(string $type){
        if(!$this->hastypeCategorie($type)) return [];
        $value_options = $this->getOption('value_options');
        return $value_options[$type];
    }
    public function setTypeCategorieOption(string $type, string $key, $value) : static
    {
        $options = $this->getTypeCategorieOptions($type);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$type] = $options;
        $this->setOptions($inputOptions);
        return  $this;
    }

    public function getTypeCategorieAttributes(string $type) : array
    {
        $typeOptions = $this->getTypeCategorieOptions($type);
        if(!key_exists('attributes', $typeOptions)){return [];}
        return $typeOptions['attributes'];
    }
    public function setTypeCategorieAttribute(string $type, $key, $value) : static
    {
        if(!$this->hastypeCategorie($type)) return $this;
        $attributes = $this->getTypeCategorieAttributes($type);
        $attributes[$key]=$value;
        $this->setTypeCategorieOption($type, 'attributes', $attributes);
        return $this;
    }

    protected function getTypeCategorieStage(CategorieStage $categorieStage) : string
    {
       return ($categorieStage->isCategoriePrincipale())  ? CategorieStage::TYPE_CATEGORIE_PRINCIPALE : CategorieStage::TYPE_CATEGORIE_SECONDAIRE ;
    }
}