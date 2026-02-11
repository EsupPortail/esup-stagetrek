<?php

namespace Application\Entity\Db;

use Application\Entity\Db;
use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * ContactTerrain
 */
class ContactTerrain implements ResourceInterface,
    HasCodeInterface, HasLibelleInterface
{
    const RESOURCE_ID = 'ContactTerrain';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    /**
     * @var bool
     */
    protected bool $isResponsableStage = false;

    use HasIdTrait;
    use HasCodeTrait;

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->contact->getCode();
    }
    public function setCode(string $code): static
    {//On ne peux pas modifier le code du contact
        return $this;
    }

    use HasLibelleTrait;
    /**
     * Get libelle.
     *
     * @return string|null
     */
    public function getLibelle(): ?string
    {
        if (!isset($this->contact)) {
            return null;
        }
        return $this->contact->getLibelle();
    }

    use HasContactTrait;
    use HasTerrainStageTrait;

    /**
     * @var bool
     */
    protected bool $isSignataireConvention = false;
    /**
     * @var int|null $prioriteOrdreSignature
     */
    protected ?int $prioriteOrdreSignature = 1;
    /**
     * @var bool
     */
    protected bool $canValiderStage = false;
    /**
     * @var bool
     */
    protected bool $sendMailAutoListeEtudiantsStage = false;
    /**
     * @var bool
     */
    protected bool $sendMailAutoValidationStage = false;
    /**
     * @var bool
     */
    protected bool $sendMailAutoRappelValidationStage = false;


    /**
     * @var bool
     * @desc est-ce que le contact recoit automatiquement les mails d'affectations sur ce terrains de stage
     */
    protected bool $sendMailAutoAffectationTerrains = false;

    /**
     * @var bool
     */
    protected bool $visibleParEtudiant = false;

    /**
     * Get isResponsableStage.
     *
     * @return bool
     */
    public function getIsResponsableStage(): bool
    {
        return $this->isResponsableStage;
    }

    public function isResponsableStage(): bool
    {
        return $this->isResponsableStage;
    }

    /**
     * Set isResponsableStage.
     *
     * @param bool $isResponsableStage
     *
     * @return ContactTerrain
     */
    public function setIsResponsableStage(bool $isResponsableStage) : static
    {
        $this->isResponsableStage = $isResponsableStage;
        return $this;
    }

    /**
     * Get isSignataireConvention.
     *
     * @return bool
     */
    public function getIsSignataireConvention(): bool
    {
        return $this->isSignataireConvention;
    }

    /**
     * Get isSignataireConvention.
     *
     * @return bool
     */
    public function isSignataireConvention(): bool
    {
        return $this->isSignataireConvention;
    }

    /**
     * Set isSignataireConvention.
     *
     * @param bool $isSignataireConvention
     *
     * @return ContactTerrain
     */
    public function setIsSignataireConvention(bool $isSignataireConvention): static
    {
        $this->isSignataireConvention = $isSignataireConvention;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrioriteOrdreSignature(): ?int
    {
        return $this->prioriteOrdreSignature;
    }

    /**
     * @param int|null $prioriteOrdreSignature
     * @return \Application\Entity\Db\ContactTerrain
     */
    public function setPrioriteOrdreSignature(?int $prioriteOrdreSignature): static
    {
        if ($prioriteOrdreSignature <= 0) {
            $prioriteOrdreSignature = null;
        }
        $this->prioriteOrdreSignature = $prioriteOrdreSignature;
        return $this;
    }

    /**
     * Get canValiderStage.
     *
     * @return bool
     */
    public function getCanValiderStage(): bool
    {
        return $this->canValiderStage;
    }

    /**
     * Set canValiderStage.
     *
     * @param bool $canValiderStage
     *
     * @return ContactTerrain
     */
    public function setCanValiderStage(bool $canValiderStage): static
    {
        $this->canValiderStage = $canValiderStage;
        return $this;
    }

    /**
     * Get canValiderStage.
     *
     * @return bool
     */
    public function canValiderStage(): bool
    {
        return $this->canValiderStage;
    }

    /**
     * Get sendMailAutoListeEtudiantsStage.
     *
     * @return bool
     */
    public function getSendMailAutoListeEtudiantsStage(): bool
    {
        return $this->sendMailAutoListeEtudiantsStage;
    }

    /**
     * Set sendMailAutoListeEtudiantsStage.
     *
     * @param bool $sendMailAutoListeEtudiantsStage
     *
     * @return ContactTerrain
     */
    public function setSendMailAutoListeEtudiantsStage(bool $sendMailAutoListeEtudiantsStage): static
    {
        $this->sendMailAutoListeEtudiantsStage = $sendMailAutoListeEtudiantsStage;
        return $this;
    }

    /**
     * Get sendMailAutoValidationStage.
     *
     * @return bool
     */
    public function getSendMailAutoValidationStage(): bool
    {
        return $this->sendMailAutoValidationStage;
    }

    /**
     * Set sendMailAutoValidationStage.
     *
     * @param bool $sendMailAutoValidationStage
     *
     * @return ContactTerrain
     */
    public function setSendMailAutoValidationStage(bool $sendMailAutoValidationStage): static
    {
        $this->sendMailAutoValidationStage = $sendMailAutoValidationStage;
        return $this;
    }

    /**
     * Get sendMailAutoRappelValidationStage.
     *
     * @return bool
     */
    public function getSendMailAutoRappelValidationStage(): bool
    {
        return $this->sendMailAutoRappelValidationStage;
    }

    /**
     * Set sendMailAutoRappelValidationStage.
     *
     * @param bool $sendMailAutoRappelValidationStage
     *
     * @return ContactTerrain
     */
    public function setSendMailAutoRappelValidationStage(bool $sendMailAutoRappelValidationStage): static
    {
        $this->sendMailAutoRappelValidationStage = $sendMailAutoRappelValidationStage;
        return $this;
    }

    public function sendMailAuto(): bool
    {
        return $this->sendMailAutoListeEtudiantsStage() || $this->sendMailAutoRappelValidationStage() || $this->sendMailAutoValidationStage();
    }

    public function sendMailAutoListeEtudiantsStage(): bool
    {
        return $this->sendMailAutoListeEtudiantsStage;
    }

    public function sendMailAutoRappelValidationStage(): bool
    {
        return $this->sendMailAutoRappelValidationStage;
    }

    public function sendMailAutoValidationStage(): bool
    {
        return $this->sendMailAutoValidationStage;
    }

    /**
     * Set contact.
     *
     * @param \Application\Entity\Db\Contact $contact
     *
     * @return ContactTerrain
     */
    public function setContact(Contact $contact = null): static
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * Get contact.
     *
     * @return \Application\Entity\Db\Contact|null
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * Get displayName.
     *
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->contact->getDisplayName();
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return ContactTerrain
     */
    public function setDisplayName(string $displayName): static
    {
        if (isset($this->contact)) {
            $this->contact->setDisplayName($displayName);
        }
        return $this;
    }

    /**
     * Get mail.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        if (!isset($this->contact)) {
            return "";
        }
        return $this->contact->getEmail();
    }

    /**
     * Set mail.
     *
     * @param string $mail
     *
     * @return ContactTerrain
     */
    public function setMail(string $mail): static
    {
        if (isset($this->contact)) {
            $this->contact->setEmail($mail);
        }
        return $this;
    }

    /**
     * Get telephone.
     *
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        if (!isset($this->contact)) {
            return "";
        }
        return $this->contact->getTelephone();
    }

    /**
     * Set telephone.
     *
     * @param string $telephone
     *
     * @return ContactTerrain
     */
    public function setTelephone(string $telephone): static
    {
        if (isset($this->contact)) {
            $this->contact->setTelephone($telephone);
        }
        return $this;
    }

    /**
     * Get actif.
     *
     * @return bool
     */
    public function isActif(): bool
    {
        if (!isset($this->contact)) {
            return false;
        }
        return $this->contact->isActif();
    }

    /**
     * Set actif.
     *
     * @param string $actif
     *
     * @return ContactTerrain
     */
    public function setActif(string $actif): static
    {
        if (isset($this->contact)) {
            $this->contact->setActif($actif);
        }
        return $this;
    }

    /**
     * Get visibleParEtudiant.
     *
     * @return bool
     */
    public function getVisibleParEtudiant(): bool
    {
        return $this->visibleParEtudiant;
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
     * Set visibleParEtudiant.
     *
     * @param bool $visibleParEtudiant
     *
     * @return ContactTerrain
     */
    public function setVisibleParEtudiant(bool $visibleParEtudiant): static
    {
        $this->visibleParEtudiant = $visibleParEtudiant;
        return $this;
    }
}
