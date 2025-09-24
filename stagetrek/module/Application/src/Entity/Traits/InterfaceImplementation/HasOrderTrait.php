<?php

namespace  Application\Entity\Traits\InterfaceImplementation;


use Application\Entity\Interfaces\HasOrderInterface;
use Doctrine\Common\Collections\Collection;

trait HasOrderTrait
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
     * @return \Application\Entity\Traits\InterfaceImplementation\HasOrderTrait
     */
    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;
        return $this;
    }

    /**
     * @param HasOrderInterface[] $entities
     */
    public static function sort(array|Collection $entities, string $order = 'asc'): array
    {
        $ordre = ($order != 'desc') ? 1 : -1;
        if($entities instanceof Collection){$entities = $entities->toArray();}
        usort($entities, function (HasOrderInterface $e1, HasOrderInterface $e2) use ($ordre){
            return $ordre*($e1->getOrdre()-$e2->getOrdre());
        });
        return $entities;
    }
}