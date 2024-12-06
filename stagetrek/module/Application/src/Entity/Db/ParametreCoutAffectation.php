<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * ParametreCoutAffectation
 */
class ParametreCoutAffectation implements ResourceInterface
{
    const RESOURCE_ID = 'ParametreCoutAffectation';

    public static function sortParametresCoutsAffectations(array $parametres = []): array
    {
        usort($parametres, function (ParametreCoutAffectation $p1,ParametreCoutAffectation $p2){
            return $p1->getRang() - $p2->getRang();
        });
        return $parametres;
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;

    /**
     * @var int
     */
    protected int $rang = 1;

    /**
     * @var int
     */
    protected int $cout = 0;


    /**
     * Set rang.
     *
     * @param int $rang
     *
     * @return ParametreCoutAffectation
     */
    public function setRang(int $rang): static
    {
        $this->rang = $rang;

        return $this;
    }

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
     * Set cout.
     *
     * @param int $cout
     *
     * @return ParametreCoutAffectation
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
}
