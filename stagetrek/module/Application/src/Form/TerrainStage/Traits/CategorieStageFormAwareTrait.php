<?php


namespace Application\Form\TerrainStage\Traits;

use Application\Form\TerrainStage\CategorieStageForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits CategorieStageFormAwareTrait
 * @package Application\Form\TerrainStage\Traits
 */
trait  CategorieStageFormAwareTrait
{
    /**
     * @var CategorieStageForm|null $categorieStageForm;
     */
    protected ?CategorieStageForm $categorieStageForm = null;

    /**
     * @return CategorieStageForm
     */
    public function getAddCategorieStageForm(): CategorieStageForm
    {
        $form = $this->categorieStageForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }
    /**
     * @return CategorieStageForm
     */
    public function getEditCategorieStageForm(): CategorieStageForm
    {
        $form = $this->categorieStageForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param CategorieStageForm $categorieStageForm
     * @return \Application\Form\TerrainStage\Traits\CategorieStageFormAwareTrait
     */
    public function setCategorieStageForm(CategorieStageForm $categorieStageForm) : static
    {
        $this->categorieStageForm = $categorieStageForm;
        return $this;
    }


}