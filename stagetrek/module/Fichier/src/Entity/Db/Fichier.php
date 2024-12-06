<?php

namespace Fichier\Entity\Db;

use UnicaenUtilisateur\Entity\Db\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareTrait;

class Fichier implements HistoriqueAwareInterface {
    use HistoriqueAwareTrait;

    /** @var string|null */
    protected ?string $id = null;
    /** @var ?string|null */
    protected ?string $nomOriginal = null;
    /** @var string|null */
    protected ?string $nomStockage = null;

    /** @var Nature|null */
    protected ?Nature $nature = null;
    /** @var string|null */
    protected ?string $typeMime = null;
    /** @var string|null */
    protected ?string $taille = null;

    /** @var string|null
     * @desc tmpName permet à la création d'un fichier de connaitre sont emplacement temporaire.
     * Par définition, il n'est pas stoqué en bdd
     */
    protected ?string $tmpName = null;

    /**
     * @return string|null
     */
    public function getId() : ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Fichier
     */
    public function setId(string $id) : static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomOriginal() : ?string
    {
        return $this->nomOriginal;
    }

    /**
     * @param string $nomOriginal
     * @return Fichier
     */
    public function setNomOriginal(string $nomOriginal) : static
    {
        $this->nomOriginal = $nomOriginal;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomStockage() : ?string
    {
        return $this->nomStockage;
    }

    /**
     * @param string $nomStockage
     * @return Fichier
     */
    public function setNomStockage(string $nomStockage) : static
    {
        $this->nomStockage = $nomStockage;
        return $this;
    }

    /**
     * @return Nature|null
     */
    public function getNature() : ?Nature
    {
        return $this->nature;
    }

    /**
     * @param Nature $nature
     * @return Fichier
     */
    public function setNature(Nature $nature) : static
    {
        $this->nature = $nature;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTypeMime() : ?string
    {
        return $this->typeMime;
    }

    /**
     * @param string $typeMime
     * @return Fichier
     */
    public function setTypeMime(string $typeMime) : static
    {
        $this->typeMime = $typeMime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTaille() : ?string
    {
        return $this->taille;
    }

    /**
     * @param string $taille
     * @return Fichier
     */
    public function setTaille(string $taille) : static
    {
        $this->taille = $taille;
        return $this;
    }

    public function getTmpName(): ?string
    {
        return $this->tmpName;
    }

    public function setTmpName(?string $tmpName): static
    {
        $this->tmpName = $tmpName;
        return $this;
    }
}