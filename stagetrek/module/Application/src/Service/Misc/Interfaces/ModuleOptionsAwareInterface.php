<?php

namespace Application\Service\Misc\Interfaces;

use Application\Service\Misc\Entity\ModuleOptions;

interface ModuleOptionsAwareInterface
{
    /**
     * @param ModuleOptions|null $moduleOptions
     */
    public function setAppOptions(?ModuleOptions $moduleOptions) : static;

    /**
     * @return ModuleOptions|null
     */
    public function getAppOptions() : ?ModuleOptions;
}