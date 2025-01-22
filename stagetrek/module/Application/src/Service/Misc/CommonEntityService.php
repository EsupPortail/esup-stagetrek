<?php


namespace Application\Service\Misc;


use Doctrine\DBAL\Result;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use RuntimeException;

abstract class CommonEntityService
    implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * ObjectRepository
     *
     * @var ObjectRepository|null
     */
    private ObjectRepository|null $repository = null;


    /**
     * Retourne la classe de l'entité courante
     *
     * @return string
     */
    abstract public function getEntityClass(): string;

    /**
     * Retourne le repository de l'entité courante
     *
     * @return ObjectRepository|null
     */
    public function getObjectRepository(): ?ObjectRepository
    {
        if (!$this->repository) {
            $this->repository = $this->getObjectManager()->getRepository($this->getEntityClass());
        }

        return $this->repository;
    }

    /**
     * Retourne une nouvelle instance de l'entité courante
     *
     * @param null $name
     * @return mixed
     */
    public function getEntityInstance($name = null): mixed
    {
        $class = ($name) ?: $this->getEntityClass();
        return new $class;
    }

    /**
     * Initialise une requête
     *
     * @param string $alias Alias d'entité
     * @return QueryBuilder
     */
    public function initQueryBuilder(string $alias): QueryBuilder
    {
        return $this->getObjectRepository()->createQueryBuilder($alias);
    }

    /**
     * Proxy method.
     *
     * @see EntityManager::beginTransaction()
     */
    public function beginTransaction(): void
    {
        $this->getObjectManager()->beginTransaction();
    }

    /**
     * Proxy method.
     *
     * @see EntityManager::commit()
     */
    public function commit(): void
    {
        $this->getObjectManager()->commit();
    }

    /**
     * Proxy method.
     *
     * @see EntityManager::rollback()
     */
    public function rollback(): void
    {
        $this->getObjectManager()->rollback();
    }

    /**
     * Exécute une requête
     *
     * @param string $sql
     * @param array $params
     * @return \Doctrine\DBAL\Result
     * @throws \Doctrine\DBAL\Exception
     */
    public function exec(string $sql, array $params = []) : Result
    {
        return $this->getObjectManager()->getConnection()->executeQuery($sql, $this->prepareParams($params));
    }

    /**
     * @throws \Exception
     */
    protected function execProcedure($procedure, $params=[]): self
    {
        try {
            $this->beginTransaction(); // suspend auto-commit
            $plsql = sprintf("call %s(%s);", $procedure, implode($params));
            $stmt = $this->getObjectManager()->getConnection()->prepare($plsql);
            $stmt->executeStatement();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Préparation des paramètres à passer à la requête
     *
     * @param array $params
     * @return array
     */
    private function prepareParams(array $params = []): array
    {
        if (null == $params) $params = [];
        foreach ($params as $n => $v) {
            if (is_object($v) && method_exists($v, 'getId')) {
                $params[$n] = $v->getId();
            }
        }

        return $params;
    }

    /**
     * Cherche une entité par son Id
     *
     * @param int $id identifiant de l'entité
     * @return null|mixed
     */
    public function find(int $id): mixed
    {
        return (null != $id)
            ? $this->getObjectRepository()->find($id)
            : null;
    }

    /**
     * Cherche toutes les instances de l'entité
     *
     * @return array
     */
    public function findAll(): array
    {
        $result = $this->getObjectRepository()->findAll();
        return $this->getList($result);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function findAllBy(array $criteria = [], array $orderBy = []): array
    {
        $result = $this->getObjectRepository()->findBy($criteria, $orderBy);
        return $this->getList($result);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return null|mixed
     */
    public function findOneBy(array $criteria = [], array $orderBy = []): mixed
    {
        return $this->getObjectRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * Cherche une entité selon un attribut et sa valeur.
     * Possibilité de rendre la recherche insensible à la casse.
     *
     * @param mixed $value valeur de l'attribut
     * @param bool $caseSensitive sensibilité à la casse
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByAttribute(mixed $value, $attribute, bool $caseSensitive = false): mixed
    {
        if (!$value) {
            return null;
        }
        if (is_numeric($value)) {
            return $this->getObjectRepository()->findOneBy([$attribute => $value]);
        }
        if ($caseSensitive) {
            return $this->getObjectRepository()->findOneBy([$attribute => $value]);
        }
        $qb = $this->getObjectRepository()->createQueryBuilder($alias = 'st');
        $qb->andWhere($qb->expr()->eq($qb->expr()->upper("$alias.$attribute"), $qb->expr()->upper(":$attribute")))
            ->setParameter($attribute, $value);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Retourne la liste des valeurs distinctes d'un attribut d'une entité
     *
     * @param $attr
     * @return array
     */
    public function getAttributeValues($attr): array
    {
        $liste = [];

        if (null == $attr) {
            return $liste;
        }

        $qb = $this->initQueryBuilder($alias = 'st');
        $qb
            ->select("$alias.$attr")
            ->distinct()
            ->addOrderBy("$alias.$attr", 'asc');

        foreach ($qb->getQuery()->getArrayResult() as $item) {
            $liste[$item[$attr]] = $item[$attr];
        }

        return $liste;
    }

    /**
     * Retourne une liste d'entités sous forme d'un tableau associatif dont les clés
     * sont les id des entités et les valeurs correspondent au champ choisi
     *
     * @param array $entities liste des entités
     * @param string $key attribut utilisé comme clé
     * @param string $value attribut utilisé pour les valeurs
     * @param string|null $entityClass classe de l'entité
     * @return array
     */
    public function getListForSelect(array $entities, string $key = 'id', string $value = 'libelle', string $entityClass = null): array
    {
        $entityClass = $entityClass ?: $this->getEntityClass();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof $entityClass
                && method_exists($entity, $kgetter = 'get' . ucfirst($key))
                && method_exists($entity, $vgetter = 'get' . ucfirst($value))) {
                $result[$entity->$kgetter()] = $entity->$vgetter();
            }
        }

        return $result;
    }

    /**
     * Retourne une liste d'entités sous forme d'un tableau associatif dont
     * les clés sont les id des entités et les valeurs les entités elles-mêmes
     *
     * @param array $entities liste des entités
     * @param string $key attribut utilisé comme clé
     * @param string|null $entityClass classe de l'entité
     * @return array
     */
    public function getList(array $entities, string $key = 'id', string $entityClass = null): array
    {
        $entityClass = $entityClass ?: $this->getEntityClass();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof $entityClass
                && method_exists($entity, $kgetter = 'get' . ucfirst($key))) {
                $result[$entity->$kgetter()] = $entity;
            }
        }
        return $result;
    }

    /**
     * Ajoute une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(mixed $entity, string $serviceEntityClass = null): mixed
    {
        if (!$serviceEntityClass) $serviceEntityClass = $this->getEntityClass();
        if (!isset($entity)) {
            throw new RuntimeException("L'entité à ajouter n'a pas été transmise.");
        }
        if (!$this->isInstanceOfEntityClass($entity, $serviceEntityClass)) {
            if (!$serviceEntityClass) $serviceEntityClass = $this->getEntityClass();
            throw new RuntimeException("L'entité transmise doit être de la classe $serviceEntityClass.");
        }
        return $this->update($entity, $serviceEntityClass);
    }
    /**
     * Ajoute/Met à jour une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        if (!$serviceEntityClass) $serviceEntityClass = $this->getEntityClass();
        if (!isset($entity)) {
            throw new RuntimeException("L'entité à mettre à jour n'a pas été transmise.");
        }
        if (!$this->isInstanceOfEntityClass($entity, $serviceEntityClass)) {
            if (!$serviceEntityClass) $serviceEntityClass = $this->getEntityClass();
            throw new RuntimeException("L'entité transmise doit être de la classe $serviceEntityClass.");
        }
        if($entity->getId() == null){
            $this->getObjectManager()->persist($entity);
        }
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($entity);
        }

        return $entity;
    }
    /**
     * Supprime une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {
        if (!$serviceEntityClass) $serviceEntityClass = $this->getEntityClass();
        if (!isset($entity)) {
            throw new RuntimeException("L'entité à mettre à supprimer n'a pas été transmise.");
        }
        if (!$this->isInstanceOfEntityClass($entity, $serviceEntityClass)) {
            if (!$serviceEntityClass) $serviceEntityClass = $this->getEntityClass();
            throw new RuntimeException("L'entité transmise doit être de la classe $serviceEntityClass.");
        }
        $this->getObjectManager()->remove($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($entity);
        }
        return $this;
    }

    /**
     * Fonction qui vérifie que l'entité est bien de la class associée au service
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return bool
     */
    protected function isInstanceOfEntityClass(mixed $entity, string $serviceEntityClass = null): bool
    {
        $entityClass = get_class($entity);
        $serviceEntityClass = $serviceEntityClass ?: $this->getEntityClass();
        return ($serviceEntityClass == $entityClass || is_subclass_of($entity, $serviceEntityClass));
    }

    //Fonction qui permet de regarder si certaines entité ont eu des changements depuis le dernier commit
    protected function hasUnitOfWorksChange(): bool
    {
        $unitOfwork = $this->getObjectManager()->getUnitOfWork();
        $unitOfwork->computeChangeSets();
        return
            !empty($unitOfwork->getScheduledEntityInsertions())
            || !empty($unitOfwork->getScheduledEntityUpdates())
            || !empty($unitOfwork->getScheduledCollectionUpdates())
            || !empty($unitOfwork->getScheduledEntityDeletions())
            || !empty($unitOfwork->getScheduledCollectionDeletions());
    }
}