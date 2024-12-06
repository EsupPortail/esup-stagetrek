<?php

namespace Application\View\Helper\Misc;
use Application\Provider\Roles\UserProvider;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\View\Helper\AbstractHelper;
use UnicaenUtilisateur\Entity\Db\User;

class ApplicationViewHelper extends AbstractHelper
{
    use ProvidesObjectManager;

    protected static ?User $appUser = null;

    public function getAppUser(): ?User
    {
        if(self::$appUser === null) {
            self::$appUser = self::getObjectManager()->getRepository(User::class)->findOneBy(['username' => UserProvider::APP_USER]);
        }
        return self::$appUser;
    }


    /**
     * @return $this
     */
    public function __invoke(): static
    {
        return $this;
    }
}