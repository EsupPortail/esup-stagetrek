<?php


namespace Application\Assertion;

use Application\Controller\Referentiel\SourceController as Controller;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ReferentielPrivilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class SourceAssertion extends AbstractAssertion
{
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();
        $source = $this->getSource();

        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX,
            Controller::ACTION_LISTER =>true,
            Controller::ACTION_AJOUTER=>$this->assertAjouter(),
            Controller::ACTION_MODIFIER=>$this->assertModifier($source),
            Controller::ACTION_SUPPRIMER=>$this->assertSupprimer($source),
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
        $source = ($entity instanceof Source) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $source = $entity->get(Source::RESOURCE_ID);
        }
        return match ($privilege) {
            ReferentielPrivilege::REFERENTIEL_SOURCE_AFFICHER => true,
            ReferentielPrivilege::REFERENTIEL_SOURCE_AJOUTER => $this->assertAjouter(),
            ReferentielPrivilege::REFERENTIEL_SOURCE_MODIFIER => $this->assertModifier($source),
            ReferentielPrivilege::REFERENTIEL_SOURCE_SUPPRIMER => $this->assertSupprimer($source),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?Source $source) : bool
    {
        return isset($source);
    }

    private function assertSupprimer(?Source $source) : bool
    {
        if(!isset($source)) return false;
        if($source->getCode() == Source::STAGETREK) return false;

        $ref = $this->getObjectManager()->getRepository(ReferentielPromo::class)->findOneBy(['source' => $source]);
        if(isset($ref)){return false;} //on ne peut pas supprimé une source utilisé

        return true;
    }

    protected function getSource() : ?Source
    {
        $id = intval($this->getParam('source', 0));
        return $this->getObjectManager()->getRepository(Source::class)->find($id);
    }

}
