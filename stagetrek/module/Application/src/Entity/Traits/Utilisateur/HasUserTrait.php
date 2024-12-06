<?php

namespace Application\Entity\Traits\Utilisateur;

use UnicaenUtilisateur\Entity\Db\User;

/**
 *
 */
trait HasUserTrait
{
    /**
     * @var User|null
     */
    protected ?User $user = null;

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return \Application\Entity\Traits\HasUserTrait
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasUser(): bool
    {
        return $this->user !== null;
    }
}