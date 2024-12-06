<?php


namespace Application\Assertion;

use Application\Controller\Stage\ValidationStageController as Controller;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Stage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\StagePrivileges;
use Application\Provider\Roles\RolesProvider;
use Application\Service\Etudiant\EtudiantService;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\NonUniqueResultException;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ValidationStageAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
//        TODO : a revoir
//        if ($action == StageController::ACTION_VALIDER) {
//            $stage = $this->getStage();
//            $token = $this->getTokenValidation();
//            return isset($stage) && isset($token);
//            //Pas de controle pour l'action validé car elle se fait dans le controleur pour que l'on sache pourquoi l'utilisateur ne peux pas valider le stage
//            //On vérifie ici uniquement que le stage et le token sont fournis
//        }

        $stage = $this->getStage();
        $token = $this->getTokenValidation();
        $role = $this->getRole();

        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->assertAfficher($stage),
            Controller::ACTION_MODIFIER => $this->assertModifier($stage),
            Controller::ACTION_VALIDER => $this->assertValider($stage, $token),
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
        $stage = ($entity instanceof Stage) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $stage = $entity->get(Stage::RESOURCE_ID);
        }
        return match ($privilege) {
            StagePrivileges::VALIDATION_STAGE_AFFICHER => $this->assertAfficher($stage),
            StagePrivileges::VALIDATION_STAGE_MODIFIER => $this->assertModifier($stage),
            default => false,
        };
    }


    private function assertAfficher(?Stage $stage) : bool
    {
        if(!isset($stage)){return false;}
        if($this->userIsStudent()){
            return $this->userIsStageOwner($stage);
        }
        return true;
    }


    private function assertModifier(?Stage $stage) : bool
    {
        if(!isset($stage)){return false;}
        $validation = $stage->getValidationStage();
        if (!isset($validation)) {return false; }

        return true;
    }

    private function assertValider(?Stage $stage, ?string $token) : bool
    {
        if(!isset($stage)){return false;}
        if(!isset($token)){return false;}
        // Les autres cas n'autorisant pas la validation sont gérer par un validateur dans le controlleur
        // Donne une explication autre que 403
        return true;
    }

    protected function userIsStudent() : bool
    {
        $role = $this->getRole();
        return $role->getRoleId() == RolesProvider::ROLE_ETUDIANT;
    }

    protected function userIsStageOwner(Stage $stage) : bool
    {
        try {
            $etudiant = $this->getEtudiantFromUser();
        } catch (NotSupported|NonUniqueResultException|NotFoundExceptionInterface|ContainerExceptionInterface) {
            return false;
        }
        return $stage->getEtudiant()->getId() == $etudiant->getId();
    }

    protected function getStage() : ?Stage
    {
        $id = intval($this->getParam('stage', 0));
        return $this->getObjectManager()->getRepository(Stage::class)->find($id);
    }
    /** @return string|null */
    protected function getTokenValidation(): ?string
    {
        return $this->getParam('token');
    }

    /**
     * @return ?Etudiant
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getEtudiantFromUser() : ?Etudiant
    {
        $utilisateur = $this->getUser();
        /** @var EtudiantService $service */
        $service = $this->getServiceManager()->get(EtudiantService::class);
        return $service->findByAttribute($utilisateur, "user", true);
    }

}
