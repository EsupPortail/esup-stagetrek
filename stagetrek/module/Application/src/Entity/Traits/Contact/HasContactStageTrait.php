<?php

namespace Application\Entity\Traits\Contact;

use Application\Entity\Db\ContactStage;

/**
 *
 */
trait HasContactStageTrait
{
    /**
     * @var \Application\Entity\Db\ContactStage|null
     */
    protected ?ContactStage $contactStage = null;

    /**
     * @return \Application\Entity\Db\ContactStage|null
     */
    public function getContactStage(): ?ContactStage
    {
        return $this->contactStage;
    }

    /**
     * @param \Application\Entity\Db\ContactStage|null $contactStage
     * @return \Application\Entity\Traits\HasContactStageTrait
     */
    public function setContactStage(?ContactStage $contactStage): static
    {
        $this->contactStage = $contactStage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContactStage(): bool
    {
        return $this->contactStage !== null;
    }
}