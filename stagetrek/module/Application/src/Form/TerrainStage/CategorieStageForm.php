<?php


namespace Application\Form\TerrainStage;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\TerrainStage\Fieldset\CategorieStageFieldset;

/**
 * Class CategorieStageForm
 * @package Application\Form\TerrainStage
 */
class CategorieStageForm extends AbstractEntityForm
{
    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(CategorieStageFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}
