<?php

namespace Application\Service\Misc\Traits;


use Application\Service\Misc\Entity\ModuleOptions;

trait ModuleOptionsAwareTrait
{
    /**
     * @var ModuleOptions|null
     */
    protected ?ModuleOptions $moduleOptions = null;


    /**
     * @param ModuleOptions|null $moduleOptions
     * @return self
     */
    public function setAppOptions(?ModuleOptions $moduleOptions): static
    {
        $this->moduleOptions = $moduleOptions;
        return $this;
    }

    /**
     * @return \Application\Service\Misc\Entity\ModuleOptions|null
     */
    public function getAppOptions() : ?ModuleOptions
    {
        return $this->moduleOptions;
    }
}