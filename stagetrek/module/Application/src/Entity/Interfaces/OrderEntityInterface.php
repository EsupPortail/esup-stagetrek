<?php

namespace  Application\Entity\Interfaces;
use Doctrine\Common\Collections\Collection;

interface OrderEntityInterface
{
    /**
     * @return int
     */
    public function getOrdre(): int;
    /**
     * @param int $ordre
     */
    public function setOrdre(int $ordre): static;

    /**
     * @param OrderEntityInterface[] $entities
     */
    public static function sort(array|Collection $entities, string $order = 'asc') : array;

}