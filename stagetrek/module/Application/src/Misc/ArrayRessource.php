<?php

namespace Application\Misc;

use Laminas\Permissions\Acl\Resource\ResourceInterface;

//Permet de fournir plusieurs entités aux assertions
class ArrayRessource implements ResourceInterface
{
    const RESOURCE_ID = 'ArrayRessource';
    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    static int $id = 1;
    //Requis car les assertions requiére un id pour être valide
    public function getId() : int {
        return self::$id++;
    }

    /** @var ResourceInterface[] $entities */
    protected ?array $entities;
    public function __construct(array $entities = null)
    {
        $this->entities = $entities;
    }

    public function add(string $key, mixed $entity): void
    {
        $this->entities[$key] = $entity;
    }
    public function remove(string $key): void
    {
        unset($this->entities[$key]);
    }

    public function get(string $key): mixed
    {
        return ($this->entities[$key]) ?? null;
    }

    /**
     * @return array|null
     */
    public function getEntities(): ?array
    {
        return $this->entities;
    }

    /**
     * @param array|null $entities
     */
    public function setEntities(?array $entities): void
    {
        $this->entities = $entities;
    }

}