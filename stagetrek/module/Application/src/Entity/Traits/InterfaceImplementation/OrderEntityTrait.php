<?php

namespace  Application\Entity\Traits\InterfaceImplementation;


use Application\Entity\Interfaces\OrderEntityInterface;
use Doctrine\Common\Collections\Collection;

trait OrderEntityTrait
{
    /** @var int $ordre */
    protected int $ordre = 1;

    /**
     * @return int
     */
    public function getOrdre(): int
    {
        return $this->ordre;
    }

    /**
     * @param int $ordre
     * @return \Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait
     */
    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;
        return $this;
    }

    /**
     * @param OrderEntityInterface[] $entities
     */
    public static function sort(array|Collection $entities, string $order = 'asc'): array
    {
        $ordre = ($order != 'desc') ? 1 : -1;
        if($entities instanceof Collection){$entities = $entities->toArray();}
        usort($entities, function (OrderEntityInterface $e1, OrderEntityInterface $e2) use ($ordre){
            return $ordre*($e1->getOrdre()-$e2->getOrdre());
        });
        return $entities;
    }
}