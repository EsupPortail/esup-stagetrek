<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\SessionStage;

/**
 *
 */
trait HasSessionStageTrait
{
    /**
     * @var \Application\Entity\Db\SessionStage|null
     */
    protected ?SessionStage $sessionStage = null;

    /**
     * @return \Application\Entity\Db\SessionStage|null
     */
    public function getSessionStage(): ?SessionStage
    {
        return $this->sessionStage;
    }

    /**
     * @param \Application\Entity\Db\SessionStage|null $sessionStage
     * @return \Application\Entity\Traits\HasSessionStageTrait
     */
    public function setSessionStage(?SessionStage $sessionStage): static
    {
        $this->sessionStage = $sessionStage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasSessionStage(): bool
    {
        return $this->sessionStage !== null;
    }
}