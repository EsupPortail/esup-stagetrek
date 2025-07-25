<?php

namespace Application\Service\Notification;

use Application\Entity\Db\Faq;
use Application\Provider\Roles\RolesProvider;
use Application\Service\Misc\CommonEntityService;
use UnicaenUtilisateur\Entity\Db\Role;

/**
 * Class FaqService
 * @package Application\Service\FAQ
 */
class FaqService extends CommonEntityService
{

    public function getEntityClass(): string
    {
        return Faq::class;
    }

    /**
     * @return array
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findAll() : array
    {
        return $this->getObjectRepository()->findBy([], ['ordre' => 'ASC']);
    }


    /**
     * @param \UnicaenUtilisateur\Entity\Db\Role|null $role
     * @return array
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findAllForRole(Role $role=null): array
    {
        $res = $this->findAll();
        if($role ==null){
            return array_filter($res, function (Faq $q){
                return $q->getRoles()->isEmpty();
            });
        }
        //Cas particulier de l'administrateur : il peut toujours les voirs même si théoriquement non (permet éventuellement de corriger un bug)
        if($role->getRoleId()==RolesProvider::ADMIN_TECH){
            return $res;
        }
        return array_filter($res, function(Faq $question) use($role){
            return ($question->getRoles()->isEmpty() || $question->getRoles()->contains($role));
        });
    }
}