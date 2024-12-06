<?php

namespace Application\Form\TerrainStage\Traits;

use Application\Form\TerrainStage\TerrainStageForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits TerrainStageFormAwareTrait
 * @package Application\Form\TerrainStage\Traits
 */
trait TerrainStageFormAwareTrait
{
    /**
     * @var TerrainStageForm|null $terrainStageForm ;
     */
    protected ?TerrainStageForm $terrainStageForm = null;

    /**
     * @return TerrainStageForm
     *
     */
    public function getAddTerrainStageForm(): TerrainStageForm
    {
        $form = $this->terrainStageForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }

    /**
     * @return TerrainStageForm
     *
     */
    public function getEditTerrainStageForm(): TerrainStageForm
    {
        $form = $this->terrainStageForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }


    /**
     * @param TerrainStageForm $terrainStageForm
     * @return \Application\Form\TerrainStage\Traits\TerrainStageFormAwareTrait
     */
    public function setTerrainStageForm(TerrainStageForm $terrainStageForm) : static
    {
        $this->terrainStageForm = $terrainStageForm;
        return $this;
    }
}