<?php

namespace Application\Entity\Traits\Parametre;

use Application\Entity\Db\ParametreCategorie;

/**
 *
 */
trait HasParametreCategorieTrait
{
    /**
     * @var ParametreCategorie|null
     */
    protected ?ParametreCategorie $parametreCategorie = null;

    /**
     * @return ParametreCategorie|null
     */
    public function getParametreCategorie(): ?ParametreCategorie
    {
        return $this->parametreCategorie;
    }


    /**
     * @param \Application\Entity\Db\ParametreCategorie|null $parametreCategorie
     * @return $this
     */
    public function setParametreCategorie(?ParametreCategorie $parametreCategorie): static
    {
        $this->parametreCategorie = $parametreCategorie;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasParametreCategorie(): bool
    {
        return $this->parametreCategorie !== null;
    }
}