<?php

namespace Application\Entity\Traits\Mail;

use UnicaenRenderer\Entity\Db\Macro;

/**
 *
 */
trait HasMacroTrait
{
    /**
     * @var \UnicaenRenderer\Entity\Db\Macro|null
     */
    protected ?Macro $macro = null;

    /**
     * @return \UnicaenRenderer\Entity\Db\Macro|null
     */
    public function getMacro(): ?Macro
    {
        return $this->macro;
    }

    /**
     * @param \UnicaenRenderer\Entity\Db\Macro|null $macro
     * @return \Application\Entity\Traits\HasMacroTrait
     */
    public function setMacro(?Macro $macro): static
    {
        $this->macro = $macro;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMacro(): bool
    {
        return $this->macro !== null;
    }
}