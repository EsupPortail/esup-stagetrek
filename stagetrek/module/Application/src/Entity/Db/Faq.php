<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasOrderInterface;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenUtilisateur\Entity\Db\Role;

/**
 * Faq
 */
class Faq implements ResourceInterface, HasOrderInterface
{
    /**
     *
     */
    const RESOURCE_ID = 'Faq';

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use HasIdTrait;
    use HasOrderTrait;

    /**
     * @var string|null
     */
    protected string|null $question = null;
    /**
     * @var string|null
     */
    protected string|null $reponse = null;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $roles;
    /**
     * @var \Application\Entity\Db\FaqCategorieQuestion|null
     */
    protected ?FaqCategorieQuestion $categorie=null;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * @param string|null $question
     * @return void
     */
    public function setQuestion(?string $question): void
    {
        $this->question = $question;
    }

    /**
     * @return string|null
     */
    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    /**
     * @param string|null $reponse
     * @return void
     */
    public function setReponse(?string $reponse): void
    {
        $this->reponse = $reponse;
    }


    /**
     * Set categorie.
     *
     * @param FaqCategorieQuestion|null $categorie
     */
    public function setCategorie(FaqCategorieQuestion $categorie = null) : void
    {
        $this->categorie = $categorie;
    }

    /**
     * @return \Application\Entity\Db\FaqCategorieQuestion|null
     */
    public function getCategorie(): ?FaqCategorieQuestion
    {
        return $this->categorie;
    }

    /**
     * @return bool
     */
    public function hasCategorie(): bool
    {
        return $this->categorie !== null;
    }

    /**
     * Get roles.
     *
     * @return Collection
     */
    public function getRoles() : Collection
    {
        return $this->roles;
    }

    /**
     * Add role.
     * @param Role $role
     * @return Faq
     */
    public function addRole(Role $role) : Faq
    {
        $this->roles[] = $role;
        return $this;
    }

    /**
     * Remove role.
     *
     * @param Role $role
     * @return \Application\Entity\Db\Faq
     */
    public function removeRole(Role $role) : static
    {
        $this->roles->removeElement($role);
        return $this;
    }
}
