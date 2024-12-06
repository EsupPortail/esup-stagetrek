<?php

namespace Application\Entity\Traits\Terrain;

use Application\Entity\Db\CategorieStage;
//TODo : supprimer les catégorie associé

/**
 *
 */
trait HasCategorieStageTrait
{
    /**
     * @var \Application\Entity\Db\CategorieStage|null
     */
    protected ?CategorieStage $categorieStage = null;

    /**
     * @return \Application\Entity\Db\CategorieStage|null
     */
    public function getCategorieStage(): ?CategorieStage
    {
        return $this->categorieStage ;
    }

    /**
     * @param \Application\Entity\Db\CategorieStage|null $categorieStage
     * @return \Application\Entity\Traits\HasCategorieStageTrait
     */
    public function setCategorieStage(?CategorieStage $categorieStage ): static
    {
        $this->categorieStage  = $categorieStage ;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasCategorieStage(): bool
    {
        return $this->categorieStage  !== null;
    }

}