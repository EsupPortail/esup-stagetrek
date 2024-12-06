<?php


namespace Application\Misc;

use Laminas\Mvc\Controller\Plugin\Params;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use UnicaenUtilisateur\Entity\Db\User;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * Traits permettant de réorienté vers des page générique de succés ou d'échec avec différentes informations
 *
 * @method isAllowed($resource, $privilege = null)
 * @method ZfcUserAuthentication zfcUserAuthentication()
 * @method FlashMessenger flashMessenger()
 * @method Params|mixed params(string $param = null, mixed $default = null)
 */
trait UserToolsTrait
{
    /************
     * Accés à l'utilisateur
     ************/
    /** @return User|null */
    protected function getUser(): ?User
    {
        return $this->zfcUserAuthentication()->getAuthService()->getIdentity()['db'];
    }
}