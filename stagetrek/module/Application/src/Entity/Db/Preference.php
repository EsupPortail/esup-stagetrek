<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Entity\Traits\Stage\HasTerrainStageSecondaireTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareTrait;

/**
 * Preference
 */
class Preference implements ResourceInterface, HistoriqueAwareInterface
{
    const RESOURCE_ID = 'Preference';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public static function sortPreferences(array $preferences): array
    {
        usort($preferences, function (Preference $p1, Preference $p2) {
            //Choix arbitraire de trier par id de session
            if ($p1->getStage()->getId() != $p2->getStage()->getId()) {
                return $p1->getId() - $p2->getId();
            } else return ($p1->getRang() - $p2->getRang());

        });
        return $preferences;
    }


    use HasIdTrait;
    use HasStageTrait;
    /**
     * @return \Application\Entity\Db\Etudiant|null
     */
    public function getEtudiant(): ?Etudiant
    {
        return ($this->hasStage()) ? $this->getStage()->getEtudiant() : null;
    }

    use HasTerrainStageTrait;
    use HasTerrainStageSecondaireTrait;
    use HistoriqueAwareTrait;

    /**
     * @var int
     */
    protected int $rang = 1;
    /**
     * @var bool
     */
    private bool $isSat = false;


    /**
     * Get rang.
     *
     * @return int
     */
    public function getRang(): int
    {
        return $this->rang;
    }

    /**
     * Set rang.
     *
     * @param int $rang
     *
     * @return Preference
     */
    public function setRang(int $rang): static
    {
        $this->rang = $rang;
        return $this;
    }

    /**
     * Get sessionStage.
     *
     * @return \Application\Entity\Db\SessionStage|null
     */
    public function getSessionStage(): ?SessionStage
    {
        if (!$this->hasStage()) return null;
        return $this->getStage()->getSessionStage();
    }

    /**
     * Set isSat.
     *
     * @param bool $isSat
     *
     * @return Preference
     */
    public function setSat(bool $isSat): static
    {
        $this->isSat = $isSat;
        return $this;
    }

    /**
     * Get isSat.
     *
     * @return bool
     */
    public function isSat(): bool
    {
        return $this->isSat;
    }

}
