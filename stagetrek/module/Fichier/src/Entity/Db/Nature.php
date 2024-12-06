<?php

namespace Fichier\Entity\Db;

class Nature {

    protected mixed $id = null;
    protected ?string $code = null;
    protected ?string $libelle = null;
    protected ?string $description = null;
    protected int $ordre = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return \Fichier\Entity\Db\Nature
     */
    public function setCode(?string $code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * @param string|null $libelle
     * @return \Fichier\Entity\Db\Nature
     */
    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return \Fichier\Entity\Db\Nature
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /** @return int|null */
    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    /**
     * @param int|null $ordre
     * @return \Fichier\Entity\Db\Nature
     */
    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;
        return $this;
    }

}