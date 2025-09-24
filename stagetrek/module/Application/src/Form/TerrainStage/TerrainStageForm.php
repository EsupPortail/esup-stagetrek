<?php

namespace Application\Form\TerrainStage;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\TerrainStage\Fieldset\TerrainStageFieldset;

/**
 * Class TerrainStageForm
 * @package Application\Form\TerrainStage
 */
class TerrainStageForm extends AbstractEntityForm
{
    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(TerrainStageFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}
