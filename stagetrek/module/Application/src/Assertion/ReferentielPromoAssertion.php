<?php


namespace Application\Assertion;

use Application\Controller\Referentiel\ReferentielPromoController as Controller;
use Application\Entity\Db\ReferentielPromo;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ReferentielPrivilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ReferentielPromoAssertion extends AbstractAssertion
{
    protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $referentielPromo = $this->getReferentielPromo();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($referentielPromo),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($referentielPromo),
            default => false,
        };
    }

    protected function assertEntity(ResourceInterface $entity, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        // si le rôle ne possède pas le privilège
        if (!parent::assertEntity($entity, $privilege)) {
            return false;
        }
        $referentiel = ($entity instanceof ReferentielPromo) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $referentiel = $entity->get(ReferentielPromo::RESOURCE_ID);
        }
        return match ($privilege) {
            ReferentielPrivilege::REFERENTIEL_PROMO_AFFICHER => true,
            ReferentielPrivilege::REFERENTIEL_PROMO_AJOUTER => $this->assertAjouter(),
            ReferentielPrivilege::REFERENTIEL_PROMO_MODIFIER => $this->assertModifier($referentiel),
            ReferentielPrivilege::REFERENTIEL_PROMO_SUPPRIMER => $this->assertSupprimer($referentiel),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?ReferentielPromo $referentiel) : bool
    {
        return isset($referentiel);
    }

    private function assertSupprimer(mixed $referentiel) : bool
    {
        return isset($referentiel);
    }

    protected function getReferentielPromo() : ?ReferentielPromo
    {
        $id = intval($this->getParam('referentielPromo', 0));
        return $this->getObjectManager()->getRepository(ReferentielPromo::class)->find($id);
    }

}
