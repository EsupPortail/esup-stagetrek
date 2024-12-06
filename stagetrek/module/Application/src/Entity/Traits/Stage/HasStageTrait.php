<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\Stage;

/**
 *
 */
trait HasStageTrait
{
    /**
     * @var \Application\Entity\Db\Stage|null
     */
    protected ?Stage $stage = null;

    /**
     * @return \Application\Entity\Db\Stage|null
     */
    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    /**
     * @param \Application\Entity\Db\Stage|null $stage
     * @return \Application\Entity\Traits\HasStageTrait
     */
    public function setStage(?Stage $stage): static
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasStage(): bool
    {
        return $this->stage !== null;
    }

}