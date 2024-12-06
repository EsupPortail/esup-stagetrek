<?php


namespace Application\Form\TerrainStage\Element;

use Application\Entity\Db\CategorieStage;
use Application\Provider\TypeStage\TypeCategorieStageProvider;
use Exception;

class CategorieStagePrincipauxSelectPicker extends CategorieStageSelectPicker
{
    /**
     * @throws \Exception
     */
    public function setDefaultData() : static
    {
       $this->useTypeCategorieData(TypeCategorieStageProvider::CATEGORIE_PRINCIPALE);
        return $this;
    }

    public function setCategoriesStages(array $categories) : static
    {
        $categories = array_filter($categories, function (CategorieStage $c){
            return $c->isCategoriePrincipale();
        });
        parent::setCategoriesStages($categories);
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function addCategorieStage(CategorieStage $categorieStage): static
    {
        if(!$categorieStage->isCategoriePrincipale()){
            throw new Exception("La catégorie de stage n'est pas une catégorie principale");
        }
        parent::addCategorieStage($categorieStage);
        return $this;
    }
}