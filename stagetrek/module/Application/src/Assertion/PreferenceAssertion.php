<?php


namespace Application\Assertion;

use Application\Controller\Preference\PreferenceController as Controller;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\Preference;
use Application\Entity\Db\Stage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Provider\Roles\RolesProvider;
use Application\Service\Etudiant\EtudiantService;
use DateTime;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class PreferenceAssertion extends AbstractAssertion
{
    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        $preference = $this->getPreference();
        $stage = $this->getStage();
        $rang = $this->getRang();

        return match ($action) {
            Controller::ACTION_LISTER,
            Controller::ACTION_LISTER_PLACES => $this->assertLister($stage),
            Controller::ACTION_AJOUTER => $this->assertAjouter($stage),
            Controller::ACTION_MODIFIER => $this->assertModifier($preference),
            Controller::ACTION_MODIFIER_PREFERENCES => $this->assertModifierPreferences($stage),
            Controller::ACTION_MODIFIER_RANG => $this->assertModifierRang($preference, $rang),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($preference),
            default => false,
        };
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    protected function assertEntity(ResourceInterface $entity, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        // si le rôle ne possède pas le privilège
        if (!parent::assertEntity($entity, $privilege)) {
            return false;
        }

        $preference = ($entity instanceof Preference) ? $entity : null;
        $stage = ($entity instanceof Stage) ? $entity : null;
        $rang = 0;
        if ($entity instanceof ArrayRessource) {
            $preference = $entity->get(Preference::RESOURCE_ID);
            $stage = $entity->get(Stage::RESOURCE_ID);
            $rang = $entity->get('rang');
        }
        //Sépéartion du priviélege pour les modification en fonction des paramètres fournis
        if($privilege ==  EtudiantPrivileges::PREFERENCE_MODIFIER
           || $privilege == EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_MODIFIER){
            if(isset($stage)){
                return $this->assertModifierPreferences($stage);
            }
            elseif(isset($rang)){
                return $this->assertModifierRang($preference, $rang);
            }
        }
        return match ($privilege) {
            EtudiantPrivileges::PREFERENCE_AFFICHER,
            EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_AFFICHER =>
                $this->assertLister($stage),
            EtudiantPrivileges::PREFERENCE_AJOUTER,
            EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_AJOUTER =>
                $this->assertAjouter($stage),
            EtudiantPrivileges::PREFERENCE_MODIFIER,
            EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_MODIFIER =>
                $this->assertModifier($preference),
            EtudiantPrivileges::PREFERENCE_SUPPRIMER,
            EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_SUPPRIMER =>
                $this->assertSupprimer($preference),
            default => false,
        };
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function assertLister(?Stage $stage) : bool
    {
        if(!isset($stage)){return false;}
        if($this->userIsStudent()){
            return $this->userIsStageOwner($stage);
        }
        return true;
    }

    /**
     * @param \Application\Entity\Db\Stage|null $stage
     * @return bool
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function assertAjouter(?Stage $stage): bool
    {
        if (!isset($stage)) {return false; }
        $rang = $stage->getPreferences()->count() + 1;
        if ($rang > $this->getRangMaxPreference()) {
            return false;
        }
        if($this->userIsStudent()){
            return $this->userIsStageOwner($stage)
                && $this->assertDatePreferences($stage);
        }
        return true;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    private function assertModifier(?Preference $preference): bool
    {
        if (!isset($preference)) {return false;}
        $stage = $preference->getStage();
        if($this->userIsStudent()){
            return $this->userIsStageOwner($stage)
                && $this->assertDatePreferences($stage);
        }
        return true;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function assertModifierRang(?Preference $preference, ?int $rang): bool
    {
        if(!isset($rang)){return false;}
        //On vérifie que l'on a le droit de modifier la préférence en question
        if(!$this->assertModifier($preference)){return false;}
        //On autorise pas a mettre un rang supérieur au nombre de préférences déjà existante
        $stage = $preference->getStage();
        if($rang > $stage->getPreferences()->count()){return false;}
        return true;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    private function assertModifierPreferences(?Stage $stage): bool
    {

        if(!isset($stage)){return false;}
        if($this->userIsStudent()){
            return $this->userIsStageOwner($stage)
                && $this->assertDatePreferences($stage);
        }
        return true;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function assertSupprimer(?Preference $preference): bool
    {
        if (!isset($preference)) {return false;}
        $stage = $preference->getStage();
        if($this->userIsStudent()){
            return $this->userIsStageOwner($stage)
                && $this->assertDatePreferences($stage);
        }
        return true;
    }

    protected function userIsStudent() : bool
    {
        $role = $this->getRole();
        return $role->getRoleId() == RolesProvider::ETUDIANT;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function userIsStageOwner(Stage $stage) : bool
    {
        $etudiant = $this->getEtudiantFromUser();
        return $stage->getEtudiant()->getId() == $etudiant->getId();
    }    

    protected function assertDatePreferences(Stage $stage) : bool
    {  
        $today = new DateTime();
        return $stage->getDateDebutChoix() < $today && $today <  $stage->getDateFinChoix();
    }

    protected function getPreference() : ?Preference
    {
        $id = intval($this->getParam('preference', 0));
        return $this->getObjectManager()->getRepository(Preference::class)->find($id);
    }

    protected function getStage() : ?Stage
    {
        $id = intval($this->getParam('stage', 0));
        return $this->getObjectManager()->getRepository(Stage::class)->find($id);
    }
    protected function getRang() : ?int
    {
        $rang = $this->getParam('rang');
        return ($rang) ? intval($rang) : null;
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
    
    public function getRangMaxPreference() : ?int
    {
        /** @var Parametre $param */
        $param = $this->getObjectManager()->getRepository(Parametre::class)->findOneBy(['code' => Parametre::NB_PREFERENCES]);
        return ($param) ? intval($param->getValue()) : 0;
    }

}