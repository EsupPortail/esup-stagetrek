<?php

namespace Application\Entity\Traits\Contact;

use Application\Entity\Db\Contact;

/**
 *
 */
trait HasContactTrait
{
    /**
     * @var \Application\Entity\Db\Contact|null
     */
    protected ?Contact $contact = null;

    /**
     * @return \Application\Entity\Db\Contact|null
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @param \Application\Entity\Db\Contact|null $contact
     * @return \Application\Entity\Traits\HasContactTrait
     */
    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContact(): bool
    {
        return $this->contact !== null;
    }

}