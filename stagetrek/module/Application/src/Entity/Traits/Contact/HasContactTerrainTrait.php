<?php

namespace Application\Entity\Traits\Contact;

use Application\Entity\Db\ContactTerrain;

/**
 *
 */
trait HasContactTerrainTrait
{
    /**
     * @var \Application\Entity\Db\ContactTerrain|null
     */
    protected ?ContactTerrain $contactTerrain = null;

    /**
     * @return \Application\Entity\Db\ContactTerrain|null
     */
    public function getContactTerrain(): ?ContactTerrain
    {
        return $this->contactTerrain;
    }

    /**
     * @param \Application\Entity\Db\ContactTerrain|null $contactTerrain
     * @return \Application\Entity\Traits\HasContactTerrainTrait
     */
    public function setContactTerrain(?ContactTerrain $contactTerrain): static
    {
        $this->contactTerrain = $contactTerrain;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContactTerrain(): bool
    {
        return $this->contactTerrain !== null;
    }
}