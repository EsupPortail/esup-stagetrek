<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\Contact\HasContactStageTrait;
use Application\Entity\Traits\Convention\HasConventionStageTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use DateTime;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/** TODO : A revoir */
/**
 * ConventionStageSignataire
 */
class ConventionStageSignataire implements ResourceInterface
{
    const RESOURCE_ID = 'ConventionStageSignataire';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }



    use HasIdTrait;
    use HasLibelleTrait;
    use HasOrderTrait;
    use HasEtudiantTrait;
    use HasStageTrait;
    use HasConventionStageTrait;
    use HasContactStageTrait;

    /**
     * @var string|null
     */
    protected ?string $displayName=null;

    //Todo : remplacer mail par email partour
    /**
     * @var string|null
     */
    private ?string $mail = null;
    /**
     * @var \DateTime|null
     */
    protected ?DateTime $dateSignature = null;
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
     * Set displayName.
     *
     * @param string|null $displayName
     *
     * @return ConventionStageSignataire
     */
    public function setDisplayName(?string $displayName): static
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get mail.
     *
     * @return string|null
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * Set mail.
     *
     * @param string|null $mail
     *
     * @return ConventionStageSignataire
     */
    public function setMail(?string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function isSignataireContactStage(): bool
    {
        return $this->hasContactStage();
    }

    public function isSignataireEtudiant(): bool
    {
        return $this->hasEtudiant();
    }
}
