<?php

namespace Application\Entity\Traits\Mail;

use UnicaenMail\Entity\Db\Mail;

/**
 *
 */
trait HasMailTrait
{
    /**
     * @var \UnicaenMail\Entity\Db\Mail|null
     */
    protected ?Mail $mail = null;

    /**
     * @return \UnicaenMail\Entity\Db\Mail|null
     */
    public function getMail(): ?Mail
    {
        return $this->mail;
    }

    /**
     * @param \UnicaenMail\Entity\Db\Mail|null $mail
     * @return \Application\Entity\Traits\HasMailTrait
     */
    public function setMail(?Mail $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMail(): bool
    {
        return $this->mail !== null;
    }
}