<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Traits\Contact\HasContactsStagesTrait;
use Application\Entity\Traits\Contact\HasContactsTerrainsTrait;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenMail\Entity\HasMailsTrait;
use UnicaenTag\Entity\Db\HasTagsInterface;
use UnicaenTag\Entity\Db\HasTagsTrait;

/**
 * Contact
 */
class Contact implements ResourceInterface
    , HasCodeInterface, HasLibelleInterface
    , HasTagsInterface
{
    const RESOURCE_ID = 'Contact';
    const CODE_ASSISTANCE = 'assistance';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function generateDefaultCode(array $param = []) : string
    {
        $uid = ($param['uid']) ?? $this->getId();
        if($uid == null){$uid = uniqid();}
        if(isset($param['prefixe'])){$prefixe = $param['prefixe'];}
        else{$prefixe = "c";}
        $separateur = ($param['sep']) ?? '-';
        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 25);
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initContactsStagesCollection();
        $this->initContactsTerrainsCollection();
    }

    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;

    use HasContactsStagesTrait;
    use HasContactsTerrainsTrait;
    use HasTagsTrait;

    use HasMailsTrait;

    /**
     * @var string|null
     */
    protected ?string $displayName = null;

    /**
     * @var string|null
     */
    protected ?string $email = null;

    /**
     * @var string|null
     */
    protected ?string $telephone = null;

    /**
     * @var bool
     */
    protected bool $visibleParEtudiant = false;

    /**
     * @var bool
     */
    protected bool $actif = true;

    /**
     * Set displayName.
     *
     * @param string|null $displayName
     *
     * @return Contact
     */
    public function setDisplayName(string $displayName = null): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * Set mail.
     *
     * @param string|null $email
     *
     * @return Contact
     */
    public function setEmail(string $email = null): static
    {
        $this->email = strtolower(trim($email));

        return $this;
    }

    /**
     * Get mail.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set telephone.
     *
     * @param string|null $telephone
     */
    public function setTelephone(string $telephone = null): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    /**
     * Get telephone.
     *
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * Get visibleParEtudiant.
     *
     * @return bool
     */
    public function isVisibleParEtudiant(): bool
    {
        return $this->visibleParEtudiant;
    }

    /**
     * Set actif.
     *
     * @param bool $actif
     * @return \Application\Entity\Db\Contact
     */
    public function setActif(bool $actif) : static
    {
        $this->actif = $actif;
        return $this;
    }

    /**
     * Get actif.
     *
     * @return bool
     */
    public function getActif(): bool
    {
        return $this->actif;
    }

    /**
     * Get actif.
     *
     * @return bool
     */
    public function isActif(): bool
    {
        return $this->actif;
    }
}
