<?php


namespace Application\Form\TerrainStage\Element;

use Application\Entity\Db\TerrainStage;
use Exception;

class TerrainStageSecondaireSelectPicker extends TerrainStageSelectPicker
{
    /**
     * @throws \Exception
     */
    public function setDefaultData() : static
    {
        $this->useTypeTerrainStageData(TerrainStage::TYPE_TERRAIN_SECONDAIRE);
        return $this;
    }

    public function setTerrainsStages(array $terrainsStages) : static
    {
        $terrainsStages = array_filter($terrainsStages, function (TerrainStage $t){
            return !$t->isTerrainPrincipal();
        });
        parent::setTerrainsStages($terrainsStages);
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function addTerrainStage(TerrainStage $terrainStage) : static
    {
        if($terrainStage->isTerrainPrincipal()){
            throw new Exception("Le terrain de de stage n'est pas un terrain secondaire");
        }
        parent::addTerrainStage($terrainStage);
        return $this;
    }
}