<?php

namespace Application\Entity\Db;


use Application\Entity\Db;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * ParametreTerrainCoutAffectationFixe
 */
class ParametreTerrainCoutAffectationFixe implements ResourceInterface
{
    const RESOURCE_ID = 'ParametreTerrainCoutAffectationFixe';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;
    use HasTerrainStageTrait;

    /**
     * @var int
     */
    protected int $cout = 0;

    /**
     * @var bool
     */
    protected bool $useCoutMedian = false;

    /**
     * Set cout.
     *
     * @param int $cout
     *
     * @return ParametreTerrainCoutAffectationFixe
     */
    public function setCout(int $cout): static
    {
        $this->cout = $cout;

        return $this;
    }

    /**
     * Get cout.
     *
     * @return int
     */
    public function getCout(): int
    {
        return $this->cout;
    }

    /**
     * Set useCoutMedian.
     *
     * @param bool $useCoutMedian
     *
     * @return ParametreTerrainCoutAffectationFixe
     */
    public function setUseCoutMedian(bool $useCoutMedian): static
    {
        $this->useCoutMedian = $useCoutMedian;

        return $this;
    }

    /**
     * Get useCoutMedian.
     *
     * @return bool
     */
    public function getUseCoutMedian(): bool
    {
        return $this->useCoutMedian;
    }

    /**
     * Set terrainStage.
     *
     * @param \Application\Entity\Db\TerrainStage|null $terrainStage
     *
     * @return ParametreTerrainCoutAffectationFixe
     */
    public function setTerrainStage(Db\TerrainStage $terrainStage = null): static
    {
        $this->terrainStage = $terrainStage;

        return $this;
    }

    /**
     * Get terrainStage.
     *
     * @return \Application\Entity\Db\TerrainStage|null
     */
    public function getTerrainStage(): ?TerrainStage
    {
        return $this->terrainStage;
    }
}
