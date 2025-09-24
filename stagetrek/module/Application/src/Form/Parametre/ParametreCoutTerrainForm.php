<?php


namespace Application\Form\Parametre;

use Application\Entity\Db\TerrainStage;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Parametre\Fieldset\ParametreCoutTerrainFieldset;

/**
 * Class ParametreCoutTerrainForm
 * @package Application\Form
 */
class ParametreCoutTerrainForm extends AbstractEntityForm
{

    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ParametreCoutTerrainFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }

    public function fixerTerrainStage(TerrainStage $terrain): static
    {
        /** @var ParametreCoutTerrainFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->fixerTerrainStage($terrain);
        return $this;
    }
}