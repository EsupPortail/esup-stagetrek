<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use DateTime;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * ContactStage
 */
class ContactStage implements ResourceInterface
{
    const RESOURCE_ID = 'ContactStage';

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
    /**
     * @var bool
     */
    protected bool $isSignataireConvention = false;


    use IdEntityTrait;
    use HasContactTrait;
    use HasStageTrait;

    /**
     * @var int|null $prioriteOrdreSignature
     */
    protected ?int $prioriteOrdreSignature = 0;
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
     */
    protected bool $visibleParEtudiant = true;
    /**
     * @var string|null
     */
    protected ?string $tokenValidation = null;
    /**
     * @var \DateTime|null
     */
    protected ?DateTime $tokenExpirationDate = null;

    /**
     * Get code.
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        if (!isset($this->contact)) {
            return null;
        }
        return $this->contact->getCode();
    }

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

    /**
     * Get displayName.
     *
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        if (!isset($this->contact)) {
            return null;
        }
        return $this->contact->getDisplayName();
    }

    /**
     * Get mail.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        if (!isset($this->contact)) {
            return null;
        }
        return $this->contact->getEmail();
    }

    /**
     * Get telephone.
     *
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        if (!isset($this->contact)) {
            return null;
        }
        return $this->contact->getTelephone();
    }

    /**
     * Get isResponsableStage.
     *
     * @return bool
     */
    public function getIsResponsableStage(): bool
    {
        return $this->isResponsableStage;
    }

    /**
     * Get isResponsableStage.
     *
     * @return bool
     */
    public function isResponsableStage(): bool
    {
        return $this->isResponsableStage;
    }

    /**
     * @param bool $isResponsableStage
     * @return \Application\Entity\Db\ContactStage
     */
    public function setIsResponsableStage(bool $isResponsableStage): static
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
     * @return \Application\Entity\Db\ContactStage
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
     * @return \Application\Entity\Db\ContactStage
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
     * Set canValiderStage.
     *
     * @param bool $canValiderStage
     * @return \Application\Entity\Db\ContactStage
     */
    public function setCanValiderStage(bool $canValiderStage) : static
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
     * @return \Application\Entity\Db\ContactStage
     */
    public function setSendMailAutoListeEtudiantsStage(bool $sendMailAutoListeEtudiantsStage) : static
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
     * @return \Application\Entity\Db\ContactStage
     */
    public function setSendMailAutoValidationStage(bool $sendMailAutoValidationStage) : static
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
     * @return \Application\Entity\Db\ContactStage
     */
    public function setSendMailAutoRappelValidationStage(bool $sendMailAutoRappelValidationStage): static
    {
        $this->sendMailAutoRappelValidationStage = $sendMailAutoRappelValidationStage;
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
     * @return \Application\Entity\Db\ContactStage
     */
    public function setVisibleParEtudiant(bool $visibleParEtudiant): static
    {
        $this->visibleParEtudiant = $visibleParEtudiant;
        return $this;
    }

    public function sendMailAuto(): bool
    {
        return $this->sendMailAutoListeEtudiantsStage() || $this->sendMailAutoRappelValidationStage() || $this->sendMailAutoValidationStage();
    }

    /**
     * Get sendMailAutoListeEtudiantsStage.
     *
     * @return bool
     */
    public function sendMailAutoListeEtudiantsStage(): bool
    {
        return $this->sendMailAutoListeEtudiantsStage;
    }

    /**
     * Get sendMailAutoRappelValidationStage.
     *
     * @return bool
     */
    public function sendMailAutoRappelValidationStage(): bool
    {
        return $this->sendMailAutoRappelValidationStage;
    }

    /**
     * Get sendMailAutoValidationStage.
     *
     * @return bool
     */
    public function sendMailAutoValidationStage(): bool
    {
        return $this->sendMailAutoValidationStage;
    }

    /**
     * Get tokenValidation.
     *
     * @return string|null
     */
    public function getTokenValidation(): ?string
    {
        return $this->tokenValidation;
    }

    /**
     * Set tokenValidation.
     *
     * @param string|null $tokenValidation
     */
    public function setTokenValidation(string $tokenValidation = null): static
    {
        $this->tokenValidation = $tokenValidation;
        return $this;
    }

    /**
     * Get tokenValidation.
     *
     * @return boolean|null
     */
    public function tokenValide(): ?bool
    {
        if (!$this->tokenValidation) {
            return false;
        }
        $today = new DateTime();
        if (!$this->tokenExpirationDate || $this->tokenExpirationDate < $today) {
            return false;
        }
        return true;
    }

    /**
     * Get tokenExpirationDate.
     *
     * @return DateTime|null
     */
    public function getTokenExpirationDate(): ?DateTime
    {
        return $this->tokenExpirationDate;
    }

    /**
     * Set tokenExpirationDate.
     *
     * @param DateTime|null $tokenExpirationDate
     */
    public function setTokenExpirationDate(DateTime $tokenExpirationDate = null): static
    {
        $this->tokenExpirationDate = $tokenExpirationDate;
        return $this;
    }
}
