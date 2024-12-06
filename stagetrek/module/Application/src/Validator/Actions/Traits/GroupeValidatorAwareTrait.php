<?php

namespace Application\Validator\Actions\Traits;

use Application\Validator\Actions\GroupeValidator;

Trait GroupeValidatorAwareTrait
{
    /** @var GroupeValidator|null $groupeValidator */
    protected ?GroupeValidator $groupeValidator = null;

    public function getGroupeValidator(): ?GroupeValidator
    {
        return $this->groupeValidator;
    }

    public function setGroupeValidator(?GroupeValidator $groupeValidator): void
    {
        $this->groupeValidator = $groupeValidator;
    }

}