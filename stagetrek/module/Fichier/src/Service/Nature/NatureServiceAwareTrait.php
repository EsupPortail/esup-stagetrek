<?php

namespace Fichier\Service\Nature;

trait NatureServiceAwareTrait {

    /** @var NatureService $natureService */
    private NatureService $natureService;

    /**
     * @return NatureService
     */
    public function getNatureService() : NatureService
    {
        return $this->natureService;
    }

    /**
     * @param NatureService $natureService
     * @return NatureService
     */
    public function setNatureService(NatureService $natureService) : NatureService
    {
        $this->natureService = $natureService;
        return $this->natureService;
    }


}