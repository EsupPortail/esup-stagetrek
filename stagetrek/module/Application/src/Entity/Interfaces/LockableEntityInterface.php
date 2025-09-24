<?php

namespace Application\Entity\Interfaces;


use Application\Entity\Db\Source;

/**
 * Entité pouvant avoir un "verroux" empêchant par exemple des suppression par erreur
 * Repose sur le tag "lock"
 */
interface LockableEntityInterface
{
    public function lock(...$param) : static;
    public function unlock() : static;
    public function isLocked() : bool;
}