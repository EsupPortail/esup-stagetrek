<?php

namespace Application\Service\Misc\Interfaces;

use Application\Entity\Interfaces\LockableEntityInterface;
use UnicaenTag\Entity\Db\Tag;

/**
 * Entité pouvant avoir un "verroux" empêchant par exemple des suppression par erreur
 * Repose sur le tag "lock"
 */
interface LockableEntityServiceInterface
{
    public function lock(LockableEntityInterface $entity) : LockableEntityInterface;
    public function unlock(LockableEntityInterface $entity) : LockableEntityInterface;
}