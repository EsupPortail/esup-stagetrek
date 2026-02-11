<?php

namespace Indicateur\Service\Perimetre;

use UnicaenIndicateur\Service\Perimetre\PerimetreServiceInterface;
use UnicaenIndicateur\Service\Perimetre\PerimetreServiceTrait;
use UnicaenUtilisateur\Entity\Db\RoleInterface;
use ZfcUser\Entity\UserInterface;

class PerimetreService implements PerimetreServiceInterface
{
    use PerimetreServiceTrait;

    public function getPerimetres(UserInterface $user, RoleInterface $role) : array
    {
        $perimetres = [];

        /** Pour gerer des périmetres sur les indicateurs (non utilisé pour le momment) ************************/
        //TERRAINS
        //ROLE
        return $perimetres;
    }

}