<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\Adresse\HasAdresseTypeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
/**
 * Adresse
 */
class Adresse implements ResourceInterface
{
    /**
     *
     */
    const RESOURCE_ID = 'Adresse';
    /**
     * @var string|null
     */
    protected ?string $adresse = null;

    use HasIdTrait;
    use HasAdresseTypeTrait;
    /**
     * @var string|null
     */
    protected ?string $ville = null;
    /**
     * @var int|null
     */
    protected ?int $villeCode = null;
    /**
     * @var string|null
     */
    protected ?string $codePostal = null;
    /**
     * @var string|null
     */
    protected ?string $complement = null;
    /**
     * @var string|null
     */
    protected ?string $cedex = null;

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    /**
     * Get adresse.
     * @return string|null
     */
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    /**
     * Set adresse.
     *
     * @param string|null $adresse
     *
     * @return Adresse
     */
    public function setAdresse(?string $adresse = null): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * Get ville.
     *
     * @return string|null
     */
    public function getVille(): ?string
    {
        return $this->ville;
    }

    /**
     * Set ville.
     *
     * @param string|null $ville
     *
     * @return Adresse
     */
    public function setVille(?string $ville = null): static
    {
        $this->ville = $ville;
        return $this;
    }

    /**
     * Get villeCode.
     *
     * @return int|null
     */
    public function getVilleCode(): ?int
    {
        return $this->villeCode;
    }

    /**
     * Set villeCode.
     *
     * @param int|null $villeCode
     *
     * @return Adresse
     */
    public function setVilleCode(?int $villeCode = null): static
    {
        $this->villeCode = $villeCode;
        return $this;
    }

    /**
     * Get codePostal.
     *
     * @return string|null
     */
    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    /**
     * Set codePostal.
     *
     * @param string|null $codePostal
     *
     * @return Adresse
     */
    public function setCodePostal(?string $codePostal = null): static
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    /**
     * Get complement.
     *
     * @return string|null
     */
    public function getComplement(): ?string
    {
        return $this->complement;
    }

    /**
     * Set complement.
     *
     * @param string|null $complement
     *
     * @return Adresse
     */
    public function setComplement(?string $complement = null): static
    {
        $this->complement = $complement;
        return $this;
    }

    /**
     * Get cedex.
     *
     * @return string|null
     */
    public function getCedex(): ?string
    {
        return $this->cedex;
    }

    /**
     * Set cedex.
     *
     * @param string|null $cedex
     *
     * @return Adresse
     */
    public function setCedex(?string $cedex = null): static
    {
        $this->cedex = $cedex;
        return $this;
    }

    //Adresse considéré comme valide si a minima champ adresse, ville et CP
    /**
     * @return bool
     */
    public function adresseIsComplete(): bool
    {
        return isset($this->adresse) && trim($this->adresse) != "" && isset($this->codePostal) && trim($this->codePostal) != "" && isset($this->ville) && trim($this->ville) != "";
    }

}
