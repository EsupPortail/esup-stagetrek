<?php

namespace Application\Service\EntityService\Traits;

namespace Application\Service\Stage\Traits;

use Application\Service\Stage\SessionStageService;

/**
 * Traits SessionStageServiceAwareTrait
 * @package Application\Service\SessionStage
 */
Trait SessionStageServiceAwareTrait
{
    /** @var SessionStageService|null $sessionStageService */
    protected ?SessionStageService $sessionStageService = null;

    /**
     * @return SessionStageService
     */
    public function getSessionStageService(): SessionStageService
    {
        return $this->sessionStageService;
    }

    /**
     * @param SessionStageService $sessionStageService
     * @return SessionStageServiceAwareTrait
     */
    public function setSessionStageService(SessionStageService $sessionStageService): static
    {
        $this->sessionStageService = $sessionStageService;
        return $this;
    }
}