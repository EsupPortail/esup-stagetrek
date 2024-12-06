<?php

namespace Application\Entity\Traits\Parametre;

use Application\Entity\Db\ParametreType;

/**
 *
 */
trait HasParametreTypeTrait
{
    /**
     * @var ParametreType|null
     */
    protected ?ParametreType $parametreType = null;

    /**
     * @return ParametreType|null
     */
    public function getParametreType(): ?ParametreType
    {
        return $this->parametreType;
    }

    /**
     * @param ParametreType|null $parametreType
     * @return HasParametreTypeTrait
     */
    public function setParametreType(?ParametreType $parametreType): static
    {
        $this->parametreType = $parametreType;
        return $this;
    }
}