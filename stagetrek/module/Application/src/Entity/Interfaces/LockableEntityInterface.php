<?php

namespace Application\Entity\Interfaces;

use UnicaenTag\Entity\Db\Tag;

/**
 * Entité pouvant avoir un "verroux" empêchant par exemple des suppression par erreur
 * Repose sur le tag "lock"
 */
interface LockableEntityInterface
{
    public function lock(Tag $tag) : static;
    public function unlock() : static;
    public function isLocked() : bool;
}