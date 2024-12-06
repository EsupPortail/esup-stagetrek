<?php

namespace Application\Assertion;

use Doctrine\ORM\EntityRepository;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\Router\RouteMatch;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;
use UnicaenPrivilege\Provider\Privilege\Privileges;
use UnicaenUtilisateur\Entity\Db\User;

abstract class AbstractAssertion extends \UnicaenPrivilege\Assertion\AbstractAssertion
    implements
    ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param \Laminas\Permissions\Acl\Resource\ResourceInterface $entity
     * @param null $privilege
     * @return boolean
     */
    //TODO : a revoir car pas clair que la vérification sur le privilége marche correctement
    protected function assertEntity(ResourceInterface $entity, $privilege = null)
    {
        /** @var \UnicaenUtilisateur\Entity\Db\Role $role */
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        // Patch pour corriger le fonctionnement aberrant suivant :
        // On passe dans l'assertion même si le rôle ne possède par le privilège !
        if (!$this->getAcl()->isAllowed($this->getRole(), Privileges::getResourceId($privilege))) {
            return false;
        }

        if (!parent::assertEntity($entity, $privilege)) {
            return false;
        }
        //Le reste sera gerer dans l'action par l'action validator
        return true;
    }

    /**
     * @var array
     */
    private array $repositories = [];

    /**
     * @return RouteMatch|null
     */
    private function getRouteMatch(): ?RouteMatch
    {
        return $this->getMvcEvent()->getRouteMatch();
    }

    /**
     * @param string $name nom du paramètre
     * @param int|null $default
     * @return mixed
     */
    protected function getParam(string $name, ?int $default = null): mixed
    {
        $routeMatch = $this->getRouteMatch();
        if(!isset($routeMatch)){return null;}
        return $routeMatch->getParam($name, $default);
    }

    /**
     * @param string $name Nom du paramètre
     * @return mixed
     */
    protected function fetchEntityParam(string $name): mixed
    {
        $repository = $this->getRepository($name);
        if ($repository === null) {
            return null;
        }
        // identifiant de l'entité
        $id = $this->getParam($name);
        if (!$id) {
            return null;
        }
        return $repository->find($id);
    }

    /**
     * @param string $name
     * @return \Doctrine\ORM\EntityRepository|null
     */
    private function getRepository(string $name): ?EntityRepository
    {
        if (!isset($this->repositories[$name])) {
            $fqcn = $this->getFullQualifiedClassName($name);
            if (!class_exists($fqcn)) {
                return null;
            }
            $this->repositories[$name] = $this->getObjectManager()->getRepository($fqcn);
        }

        return $this->repositories[$name];
    }

    /**
     * @param $name
     * @return string
     */
    private function getFullQualifiedClassName($name): string
    {
        $namespace = 'Application\\Entity';
        return $namespace . sprintf('\\%s', ucfirst($name));
    }

    /**
     * Permet de faire appels aux différents service depuis les assertions
     * et nottament de ne pas réimplémenter les fonctions de test
     */
    /** @var ServiceManager $serviceManager */
    private ServiceManager $serviceManager;

    /** @return ServiceManager|null */
    public function getServiceManager(): ?ServiceManager
    {
        return $this->serviceManager;
    }

    /** @param ServiceManager $serviceManager */
    public function setServiceManager(ServiceManager $serviceManager): void
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Permet de faire appels aux différents services depuis les assertions
     * et nottament de ne pas réimplémenter les fonctions de test
     */
    /** @var ValidatorPluginManager $validatorManager */
    private ValidatorPluginManager $validatorManager;

    /** @return ValidatorPluginManager|null */
    public function getValidatorManager(): ?ValidatorPluginManager
    {
        return $this->validatorManager;
    }

    /** @param ValidatorPluginManager $validatorManager */
    public function setValidatorManager(ValidatorPluginManager $validatorManager): void
    {
        $this->validatorManager = $validatorManager;
    }

    protected ?User $user = null;

    /**
     * @return \UnicaenUtilisateur\Entity\Db\User|null $user
     */
    protected function getUser(): ?User
    {
        if (null === $this->user) {
            $this->user = $this->serviceUserContext->getIdentity()['db'];
        }

        return $this->user;
    }
}