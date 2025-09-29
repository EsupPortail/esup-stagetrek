<?php


namespace Application\Service\Misc\Traits;

use Application\Entity\Interfaces\LockableEntityInterface;
use Application\Provider\Tag\TagProvider;
use DoctrineModule\Persistence\ProvidesObjectManager;
use UnicaenTag\Service\Tag\TagServiceAwareTrait;

Trait LockableEntityServiceTrait
{
    use ProvidesObjectManager;
    use TagServiceAwareTrait;

    public function lock(LockableEntityInterface $entity): LockableEntityInterface
    {
        $tag = $this->getTagService()->getTagByCode(TagProvider::ETAT_LOCK);
        $entity->lock($tag);
        $this->getObjectManager()->flush($entity);
        return $entity;
    }

    public function unlock(LockableEntityInterface $entity) : LockableEntityInterface
    {
        $entity->unlock();
        $this->getObjectManager()->flush($entity);
        return $entity;
    }
}