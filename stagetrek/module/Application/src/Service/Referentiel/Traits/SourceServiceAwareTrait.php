<?php

namespace Application\Service\Referentiel\Traits;

use Application\Service\Referentiel\SourceService;

trait SourceServiceAwareTrait
{
    /** @var \Application\Service\Referentiel\SourceService|null */
    protected ?SourceService $sourceService = null;

    public function getSourceService(): SourceService
    {
        return $this->sourceService;
    }

    public function setSourceService(SourceService $sourceService): static
    {
        $this->sourceService = $sourceService;
        return $this;
    }

}